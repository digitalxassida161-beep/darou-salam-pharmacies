@extends('layouts.app')
@section('title', 'Nouvelle entrée')
@section('content')
<div class="card"><h1>Enregistrer une entrée de stock</h1>
<form method="POST" action="{{ route('lots.store') }}">@csrf
<label>Médicament *</label><select name="medicine_id" required><option value="">Choisir…</option>@foreach($medicines as $m)<option value="{{ $m->id }}">{{ $m->name }} — {{ $m->strength }}</option>@endforeach</select>
<div class="grid"><div><label>Numéro de lot *</label><input name="lot_number" required></div><div><label>Date de péremption *</label><input type="date" name="expiry_date" min="{{ today()->format('Y-m-d') }}" required></div><div><label>Quantité reçue *</label><input type="number" name="quantity" min="1" required></div><div><label>Prix d'achat unitaire (F CFA) *</label><input type="number" name="purchase_price" min="0" step="1" required></div><div><label>Fournisseur</label><input name="supplier"></div></div>
<button>Enregistrer l'entrée</button> <a class="btn btn-secondary" href="{{ route('lots.index') }}">Annuler</a>
</form></div>
@endsection
