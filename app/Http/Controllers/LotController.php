<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineLot;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    public function index()
    {
        $lots = MedicineLot::with('medicine')->orderBy('expiry_date')->paginate(25);
        return view('lots.index', compact('lots'));
    }

    public function create()
    {
        return view('lots.create', ['medicines' => Medicine::where('active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'lot_number' => ['required', 'string', 'max:100'],
            'expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:150'],
        ]);

        DB::transaction(function () use ($data) {
            $lot = MedicineLot::create($data);
            StockMovement::create([
                'medicine_id' => $lot->medicine_id,
                'medicine_lot_id' => $lot->id,
                'user_id' => auth()->id(),
                'type' => 'entrée',
                'quantity' => $lot->quantity,
                'note' => 'Réception du lot '.$lot->lot_number,
            ]);
        });

        return redirect()->route('lots.index')->with('success', 'Entrée de stock enregistrée.');
    }
}
