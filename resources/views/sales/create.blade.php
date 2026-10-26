@extends('layouts.app')
@section('title', 'Nouvelle vente')
@section('content')
<div class="card"><h1>Nouvelle vente</h1>
<p class="muted">Choisissez les médicaments et les quantités. Le stock sera contrôlé avant validation.</p>
<form method="POST" action="{{ route('ventes.store') }}" id="sale-form">@csrf
<div class="grid"><div><label>Référence patient (facultatif)</label><input name="patient_label" maxlength="120" placeholder="Ex. initiales ou numéro de dossier"></div><div><label>Mode de paiement *</label><select name="payment_method" required><option value="espèces">Espèces</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="autre">Autre</option></select></div></div>
<div id="items">
<div class="grid sale-item">
<div><label>Médicament *</label><select name="medicine_id[]" required><option value="">Choisir…</option>@foreach($medicines as $m)<option value="{{ $m->id }}">{{ $m->name }} — {{ $m->strength }} ({{ number_format((float)$m->sale_price,0,',',' ') }} F/unité)</option>@endforeach</select></div>
<div><label>Quantité *</label><input type="number" name="quantity[]" min="1" value="1" required></div>
</div>
</div>
<button type="button" class="btn-secondary" id="add-item">+ Ajouter un médicament</button>
<br><br><button type="submit">Valider la vente</button> <a class="btn btn-secondary" href="{{ route('ventes.index') }}">Annuler</a>
</form></div>
<script>
document.getElementById('add-item').addEventListener('click', function(){
 const first=document.querySelector('.sale-item');
 const clone=first.cloneNode(true);
 clone.querySelector('select').value='';
 clone.querySelector('input').value='1';
 document.getElementById('items').appendChild(clone);
});
</script>
@endsection
