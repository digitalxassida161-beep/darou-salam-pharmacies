<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::withSum(['lots as stock_total' => function ($q) {
            $q->whereDate('expiry_date', '>=', today());
        }], 'quantity')->orderBy('name')->paginate(20);

        return view('medicines.index', compact('medicines'));
    }

    public function create() { return view('medicines.create', ['medicine' => new Medicine()]); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Medicine::create($data);
        return redirect()->route('medicaments.index')->with('success', 'Médicament ajouté.');
    }

    public function edit(Medicine $medicament)
    {
        return view('medicines.create', ['medicine' => $medicament]);
    }

    public function update(Request $request, Medicine $medicament)
    {
        $medicament->update($this->validated($request));
        return redirect()->route('medicaments.index')->with('success', 'Médicament modifié.');
    }

    public function destroy(Medicine $medicament)
    {
        if ($medicament->lots()->where('quantity', '>', 0)->exists()) {
            return back()->withErrors(['stock' => 'Impossible d’archiver ce médicament : son stock n’est pas nul.']);
        }
        $medicament->update(['active' => false]);
        return redirect()->route('medicaments.index')->with('success', 'Médicament archivé.');
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
        ]) + ['active' => $request->boolean('active', true)];
    }
}
