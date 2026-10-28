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
    private function licenseId(): int
    {
        $licenseId = auth()->user()->license_id;

        abort_if(
            !$licenseId,
            403,
            'Aucune pharmacie associée à ce compte.'
        );

        return (int) $licenseId;
    }

    /**
     * Afficher uniquement les ventes de la pharmacie connectée.
     */
   
/**
 * Historique des ventes selon le rôle de l'utilisateur.
 */
public function index()
{
    $licenseId = $this->licenseId();
    $user = auth()->user();

    $query = Sale::where('license_id', $licenseId)
        ->with('user', 'items.medicine');

    // Un caissier ne voit que ses propres ventes.
    if ($user->role === 'caissier') {
        $query->where('user_id', $user->id);
    }

    $sales = $query
        ->latest()
        ->paginate(25);

    return view('sales.index', compact('sales'));
}


    /**
     * Préparer une vente avec les médicaments de la pharmacie.
     */
    public function create()
    {
        $licenseId = $this->licenseId();

        $medicines = Medicine::where('license_id', $licenseId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('medicines'));
    }

    /**
     * Enregistrer une vente sans toucher au stock d'une autre pharmacie.
     */
    public function store(Request $request)
    {
        $licenseId = $this->licenseId();

        $data = $request->validate([
            'patient_label' => ['nullable', 'string', 'max:120'],
            'payment_method' => [
                'required',
                'in:espèces,wave,orange_money,autre'
            ],
            'medicine_id' => ['required', 'array', 'min:1'],
            'medicine_id.*' => ['required', 'integer', 'distinct'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'integer', 'min:1'],
        ]);

        if (count($data['medicine_id']) !== count($data['quantity'])) {
            throw ValidationException::withMessages([
                'quantity' => 'Les quantités ne correspondent pas aux médicaments sélectionnés.',
            ]);
        }

        DB::transaction(function () use ($data, $licenseId) {
            $pharmacy = \App\Models\License::findOrFail($licenseId);

            $prefix = $licenseId === 1 ? 'C4' : 'DS';

            $sale = Sale::create([
                'license_id' => $licenseId,
                'receipt_number' => $prefix . '-' .
                    now()->format('Ymd-His') . '-' .
                    Str::upper(Str::random(4)),
                'user_id' => auth()->id(),
                'patient_label' => $data['patient_label'] ?? null,
                'total_amount' => 0,
                'payment_method' => $data['payment_method'],
            ]);

            $total = 0;

            foreach ($data['medicine_id'] as $i => $medicineId) {
                $qty = (int) $data['quantity'][$i];

                // Le médicament doit appartenir à cette pharmacie.
                $medicine = Medicine::where('id', $medicineId)
                    ->where('license_id', $licenseId)
                    ->lockForUpdate()
                    ->first();

                if (!$medicine || !$medicine->active) {
                    throw ValidationException::withMessages([
                        'medicine_id' => 'Ce médicament est indisponible ou n’appartient pas à votre pharmacie.',
                    ]);
                }

                // Sélectionner uniquement les lots de cette pharmacie
                // associés au médicament sélectionné.
                $lots = MedicineLot::where('license_id', $licenseId)
                    ->where('medicine_id', $medicine->id)
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', today())
                    ->orderBy('expiry_date')
                    ->lockForUpdate()
                    ->get();

                $available = $lots->sum('quantity');

                if ($available < $qty) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stock insuffisant pour {$medicine->name}. Disponible : {$available}.",
                    ]);
                }

                $remaining = $qty;

                foreach ($lots as $lot) {
                    if ($remaining <= 0) {
                        break;
                    }

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
                        'license_id' => $licenseId,
                        'medicine_id' => $medicine->id,
                        'medicine_lot_id' => $lot->id,
                        'user_id' => auth()->id(),
                        'type' => 'vente',
                        'quantity' => -$taken,
                        'note' => 'Vente ' . $sale->receipt_number,
                    ]);

                    $total += $lineTotal;
                    $remaining -= $taken;
                }
            }

            $sale->update([
                'total_amount' => $total,
            ]);
        });

        return redirect()
            ->route('ventes.index')
            ->with('success', 'Vente enregistrée et stock mis à jour.');
    }
}
