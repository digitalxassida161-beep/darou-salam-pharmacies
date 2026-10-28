<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineLot;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LotController extends Controller
{
    /**
     * Récupère la pharmacie du compte connecté.
     */
    private function licenseId(): int
    {
        $licenseId = auth()->user()?->license_id;

        abort_if(
            !$licenseId,
            403,
            'Aucune pharmacie associée à ce compte.'
        );

        return (int) $licenseId;
    }

    /**
     * Liste les lots de la pharmacie connectée.
     */
    public function index(Request $request)
    {
        $licenseId = $this->licenseId();

        $query = MedicineLot::query()
            ->where('license_id', $licenseId)
            ->whereHas('medicine', function ($q) use ($licenseId) {
                $q->where('license_id', $licenseId);
            })
            ->with('medicine');

        // Recherche par nom de médicament ou numéro de lot.
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('lot_number', 'like', "%{$search}%")
                    ->orWhereHas('medicine', function ($medicineQuery) use ($search) {
                        $medicineQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrage par état de péremption.
        $etat = $request->input('etat', 'tous');

        if ($etat === 'perime') {
            $query->whereDate('expiry_date', '<', today());
        } elseif ($etat === 'bientot') {
            $query->whereDate('expiry_date', '>=', today())
                ->whereDate('expiry_date', '<=', today()->copy()->addDays(30));
        } elseif ($etat === 'valide') {
            $query->whereDate('expiry_date', '>', today()->copy()->addDays(30));
        } elseif ($etat === 'sans_date') {
            $query->whereNull('expiry_date');
        }

        $lots = $query
            ->orderBy('expiry_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // Statistiques calculées sur tous les lots de cette pharmacie.
        $baseQuery = MedicineLot::query()
            ->where('license_id', $licenseId)
            ->whereHas('medicine', function ($q) use ($licenseId) {
                $q->where('license_id', $licenseId);
            });

        $nombreTotal = (clone $baseQuery)->count();

        $quantiteTotale = (int) (clone $baseQuery)->sum('quantity');

        $lotsPerimes = (clone $baseQuery)
            ->whereDate('expiry_date', '<', today())
            ->count();

        $lotsBientotPerimes = (clone $baseQuery)
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->copy()->addDays(30))
            ->count();

        return view('lots.index', compact(
            'lots',
            'nombreTotal',
            'quantiteTotale',
            'lotsPerimes',
            'lotsBientotPerimes',
            'etat'
        ));

        $search = trim(request('search', ''));

$query = MedicineLot::where('license_id', $licenseId)
    ->whereHas('medicine', function ($q) use ($licenseId) {
        $q->where('license_id', $licenseId);
    })
    ->with('medicine');

if ($search !== '') {
    $query->whereHas('medicine', function ($q) use ($search) {
        $q->where('name', 'like', '%' . $search . '%');
    });
}

$lots = $query
    ->orderBy('expiry_date')
    ->paginate(25)
    ->withQueryString();
    }

    /**
     * Formulaire de nouvelle entrée de stock.
     */
    public function create()
    {
        $licenseId = $this->licenseId();

        $medicines = Medicine::query()
            ->where('license_id', $licenseId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('lots.create', compact('medicines'));
    }

    /**
     * Enregistre un lot et son mouvement de stock.
     */
    public function store(Request $request)
    {
        $licenseId = $this->licenseId();

        $data = $request->validate([
            'medicine_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($licenseId) {
                    $exists = Medicine::query()
                        ->where('id', $value)
                        ->where('license_id', $licenseId)
                        ->where('active', true)
                        ->exists();

                    if (!$exists) {
                        $fail(
                            'Ce médicament ne fait pas partie de votre pharmacie ou est inactif.'
                        );
                    }
                },
            ],
            'lot_number' => ['required', 'string', 'max:100'],
            'expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:150'],
        ]);

        DB::transaction(function () use ($data, $licenseId) {
            $medicine = Medicine::query()
                ->where('id', $data['medicine_id'])
                ->where('license_id', $licenseId)
                ->where('active', true)
                ->lockForUpdate()
                ->first();

            if (!$medicine) {
                throw ValidationException::withMessages([
                    'medicine_id' => 'Le médicament sélectionné est indisponible dans votre pharmacie.',
                ]);
            }

            $lot = MedicineLot::create([
                'license_id' => $licenseId,
                'medicine_id' => $medicine->id,
                'lot_number' => $data['lot_number'],
                'expiry_date' => $data['expiry_date'],
                'quantity' => $data['quantity'],
                'purchase_price' => $data['purchase_price'],
                'supplier' => $data['supplier'] ?? null,
            ]);

            StockMovement::create([
                'license_id' => $licenseId,
                'medicine_id' => $medicine->id,
                'medicine_lot_id' => $lot->id,
                'user_id' => auth()->id(),
                'type' => 'entrée',
                'quantity' => $lot->quantity,
                'note' => 'Réception du lot ' . $lot->lot_number,
            ]);
        });

        return redirect()
            ->route('lots.index')
            ->with('success', 'Entrée de stock enregistrée.');
    }
}
