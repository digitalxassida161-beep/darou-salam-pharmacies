<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Médicaments de la pharmacie connectée uniquement.
     */
    private function pharmacyMedicines()
    {
        $licenseId = auth()->user()->license_id;

        abort_if(!$licenseId, 403, 'Aucune pharmacie associée à ce compte.');

        return Medicine::where('license_id', $licenseId);
    }

    public function index()
    {
        $medicines = $this->pharmacyMedicines()
            ->withSum(['lots as stock_total' => function ($q) {
                $q->whereDate('expiry_date', '>=', today());
            }], 'quantity')
            ->orderBy('name')
            ->paginate(20);

        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('medicines.create', [
            'medicine' => new Medicine()
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // La pharmacie est déterminée par le compte connecté,
        // jamais par un champ envoyé depuis le formulaire.
        $data['license_id'] = auth()->user()->license_id;

        $this->pharmacyMedicines()->create($data);

        return redirect()
            ->route('medicaments.index')
            ->with('success', 'Médicament ajouté.');
    }

    public function edit(Medicine $medicament)
    {
        $this->authorizePharmacy($medicament);

        return view('medicines.create', [
            'medicine' => $medicament
        ]);
    }

    public function update(Request $request, Medicine $medicament)
    {
        $this->authorizePharmacy($medicament);

        $medicament->update($this->validated($request));

        return redirect()
            ->route('medicaments.index')
            ->with('success', 'Médicament modifié.');
    }

    public function destroy(Medicine $medicament)
    {
        $this->authorizePharmacy($medicament);

        $hasValidStock = $medicament->lots()
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '>=', today())
            ->exists();

        if ($hasValidStock) {
            return back()->withErrors([
                'stock' => 'Impossible d’archiver ce médicament : il possède encore un lot valide en stock.'
            ]);
        }

        $medicament->update(['active' => false]);

        return redirect()
            ->route('medicaments.index')
            ->with('success', 'Médicament archivé.');
    }

    private function authorizePharmacy(Medicine $medicament): void
    {
        abort_if(
            !$medicament->license_id ||
            $medicament->license_id !== auth()->user()->license_id,
            403,
            'Ce médicament n’appartient pas à votre pharmacie.'
        );
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'form' => ['nullable', 'string', 'max:80'],
            'strength' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]) + [
            'active' => $request->boolean('active', true)
        ];
    }
}
