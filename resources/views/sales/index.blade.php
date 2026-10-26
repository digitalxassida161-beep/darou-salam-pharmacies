@extends('layouts.app')
@section('title', 'Ventes')
@section('content')
<div class="title-row"><h1>Historique des ventes</h1><a class="btn" href="{{ route('ventes.create') }}">+ Nouvelle vente</a></div>
<div class="card table-wrap"><table><thead><tr><th>Reçu</th><th>Date</th><th>Vendeur</th><th>Patient (facultatif)</th><th>Mode de paiement</th><th>Total</th></tr></thead><tbody>
@forelse($sales as $sale)
<tr><td>{{ $sale->receipt_number }}</td><td>{{ $sale->created_at->format('d/m/Y H:i') }}</td><td>{{ $sale->user->name }}</td><td>{{ $sale->patient_label ?: '—' }}</td><td>{{ $sale->payment_method }}</td><td>{{ number_format((float)$sale->total_amount,0,',',' ') }} F CFA</td></tr>
@empty<tr><td colspan="6">Aucune vente enregistrée.</td></tr>@endforelse
</tbody></table></div>{{ $sales->links() }}
<p class="muted">Pour préserver la traçabilité, les ventes ne peuvent pas être supprimées depuis cette interface.</p>
@endsection
