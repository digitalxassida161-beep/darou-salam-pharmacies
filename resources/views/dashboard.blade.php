
@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    $user = auth()->user();
    $pharmacy = $user->license;
@endphp

@if($user->role === 'super_admin')
    <div class="card">
        <h2>Espace Super Administrateur</h2>
        <p>Bienvenue dans l'espace central de gestion des pharmacies clientes.</p>

        <a href="{{ route('admin.licenses.index') }}" class="btn">
            Gérer les pharmacies et les licences
        </a>
    </div>
@else
    <div class="card">
        <h1>{{ $pharmacy?->pharmacy_name ?? 'Pharmacie non configurée' }}</h1>
        <p class="muted">
            {{ $pharmacy?->pharmacy_address ?? '' }}
        </p>
        <p class="muted">
            Bienvenue {{ $user->name }} — rôle : {{ $user->role }}
        </p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="muted">Ventes du jour</div>
            <h2>{{ number_format((float) $todaySales, 0, ',', ' ') }} F CFA</h2>
        </div>

        <div class="card">
            <div class="muted">Nombre de ventes aujourd'hui</div>
            <h2>{{ $salesCount }}</h2>
        </div>

        <div class="card">
            <div class="muted">Médicaments actifs</div>
            <h2>{{ $medicineCount }}</h2>
        </div>

        <div class="card">
            <div class="muted">Alertes de stock faible</div>
            <h2>{{ $lowStock->count() }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="title-row">
            <h2>Stock faible</h2>
            <a class="btn" href="{{ route('medicaments.index') }}">
                Voir les médicaments
            </a>
        </div>

        @if($lowStock->isEmpty())
            <p>Aucune alerte de stock faible.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Médicament</th>
                            <th>Stock</th>
                            <th>Seuil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStock as $m)
                            <tr>
                                <td>{{ $m->name }}</td>
                                <td>{{ (int) $m->stock_total }}</td>
                                <td>{{ $m->minimum_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Lots proches de la péremption (90 jours)</h2>

        @if($expiringLots->isEmpty())
            <p>Aucun lot proche de la péremption.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Médicament</th>
                            <th>Lot</th>
                            <th>Quantité</th>
                            <th>Péremption</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringLots as $lot)
                            <tr>
                                <td>{{ $lot->medicine->name }}</td>
                                <td>{{ $lot->lot_number }}</td>
                                <td>{{ $lot->quantity }}</td>
                                <td>{{ $lot->expiry_date->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
@endsection
