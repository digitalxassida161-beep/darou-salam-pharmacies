@extends('layouts.app')
@section('title', $medicine->exists ? 'Modifier un médicament' : 'Ajouter un médicament')
@section('content')
<div class="card">
<h1>{{ $medicine->exists ? 'Modifier le médicament' : 'Ajouter un médicament' }}</h1>
<form method="POST" action="{{ $medicine->exists ? route('medicaments.update',$medicine) : route('medicaments.store') }}">
@csrf @if($medicine->exists) @method('PUT') @endif
<div class="grid">
<div><label>Nom *</label><input name="name" required value="{{ old('name',$medicine->name) }}"></div>
<div><label>Forme (comprimé, sirop…)</label><input name="form" value="{{ old('form',$medicine->form) }}"></div>
<div><label>Dosage</label><input name="strength" value="{{ old('strength',$medicine->strength) }}"></div>
<div><label>Catégorie</label><input name="category" value="{{ old('category',$medicine->category) }}"></div>
<div><label>Unité *</label><input name="unit" required value="{{ old('unit',$medicine->unit ?: 'boîte') }}"></div>
<div><label>Prix de vente (F CFA) *</label><input type="number" min="0" step="1" name="sale_price" required value="{{ old('sale_price',$medicine->sale_price ?? 0) }}"></div>
<div><label>Seuil minimum *</label><input type="number" min="0" name="minimum_stock" required value="{{ old('minimum_stock',$medicine->minimum_stock ?? 0) }}"></div>
</div>
<label><input type="checkbox" style="width:auto" name="active" value="1" {{ old('active',$medicine->active ?? true) ? 'checked' : '' }}> Médicament actif</label><br><br>
<button>Enregistrer</button> <a class="btn btn-secondary" href="{{ route('medicaments.index') }}">Annuler</a>
</form>
</div>
@endsection
