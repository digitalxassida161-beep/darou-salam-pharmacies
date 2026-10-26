<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineLot;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
  
public function index()
{
    $query = Sale::with('user', 'items.medicine');

    // L'administrateur voit toutes les ventes.
    // Les autres utilisateurs voient uniquement leurs ventes.
    if (!auth()->user()->isAdmin()) {
        $query->where('user_id', auth()->id());
    }

    $sales = $query->latest()->paginate(25);

    return view('sales.index', compact('sales'));
}

    public function create()
    {
        $medicines = Medicine::where('active', true)->orderBy('name')->get();
        return view('sales.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_label' => ['nullable', 'string', 'max:120'],
            'payment_method' => ['required', 'in:espèces,wave,orange_money,autre'],
            'medicine_id' => ['required', 'array', 'min:1'],
            'medicine_id.*' => ['required', 'distinct', 'exists:medicines,id'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'receipt_number' => 'DS-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4)),
                'user_id' => auth()->id(),
                'patient_label' => $data['patient_label'] ?? null,
                'total_amount' => 0,
                'payment_method' => $data['payment_method'],
            ]);

            $total = 0;
            foreach ($data['medicine_id'] as $i => $medicineId) {
                $qty = (int) $data['quantity'][$i];
                $medicine = Medicine::whereKey($medicineId)
                    ->lockForUpdate()
                    ->first();

                if (!$medicine || !$medicine->active) {
                    throw ValidationException::withMessages([
                        'medicine_id' => 'Ce médicament est archivé ou indisponible à la vente.',
                    ]);
                }
                $remaining = $qty;
                $lots = MedicineLot::where('medicine_id', $medicine->id)
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', today())
                    ->orderBy('expiry_date')
                    ->lockForUpdate()->get();

                $available = $lots->sum('quantity');
                if ($available < $qty) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stock insuffisant pour {$medicine->name}. Disponible : {$available}.",
                    ]);
                }

                foreach ($lots as $lot) {
                    if ($remaining <= 0) break;
                    $taken = min($remaining, $lot->quantity);
                    $lot->decrement('quantity', $taken);
                    $lineTotal = $taken * (float) $medicine->sale_price;
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'medicine_id' => $medicine->id,
                        'medicine_lot_id' => $lot->id,
                        'quantity' => $taken,
                        'unit_price' => $medicine->sale_price,
                        'line_total' => $lineTotal,
                    ]);
                    StockMovement::create([
                        'medicine_id' => $medicine->id,
                        'medicine_lot_id' => $lot->id,
                        'user_id' => auth()->id(),
                        'type' => 'vente',
                        'quantity' => -$taken,
                        'note' => 'Vente '.$sale->receipt_number,
                    ]);
                    $total += $lineTotal;
                    $remaining -= $taken;
                }
            }
            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('ventes.index')->with('success', 'Vente enregistrée et stock mis à jour.');
    }
}
