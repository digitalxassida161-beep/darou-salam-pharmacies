
@extends('layouts.app')

@section('title', 'Lots et entrées de stock')

@section('content')
<style>
    .stock-page {
        color: #1f2937;
    }

    .stock-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .stock-header h1 {
        margin: 0 0 6px;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .stock-subtitle {
        color: #6b7280;
        margin: 0;
        font-size: 14px;
    }

    .stock-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 18px;
        background: #075e54;
        color: #fff !important;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(7, 94, 84, .15);
        transition: .2s ease;
    }

    .stock-btn:hover {
        background: #064b44;
        transform: translateY(-1px);
    }

    .stock-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stock-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
    }

    .stock-stat-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .stock-stat-value {
        font-size: 27px;
        font-weight: 800;
        line-height: 1.2;
    }

    .stock-stat-note {
        color: #9ca3af;
        font-size: 12px;
        margin-top: 7px;
    }

    .stock-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
    }

    .stock-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 20px 22px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .stock-panel-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
    }

    .stock-count {
        background: #ecfdf5;
        color: #047857;
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .stock-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .stock-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .stock-table th {
        background: #f8fafc;
        color: #64748b;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .5px;
        font-weight: 800;
        padding: 14px 18px;
        text-align: left;
        white-space: nowrap;
    }

    .stock-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }

    .stock-table tbody tr:hover {
        background: #f8fafc;
    }

    .stock-table tbody tr:last-child td {
        border-bottom: none;
    }

    .stock-medicine {
        font-weight: 700;
        color: #111827;
    }

    .stock-lot {
        display: inline-block;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        padding: 5px 8px;
        border-radius: 6px;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .stock-quantity {
        font-weight: 800;
        color: #111827;
    }

    .stock-price {
        font-weight: 700;
        white-space: nowrap;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .stock-valid {
        background: #dcfce7;
        color: #166534;
    }

    .stock-expired {
        background: #fee2e2;
        color: #991b1b;
    }

    .stock-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .stock-empty {
        text-align: center;
        padding: 50px 20px !important;
        color: #6b7280;
    }

    .stock-empty-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .stock-pagination {
        padding: 18px 22px;
        border-top: 1px solid #e5e7eb;
    }

    .stock-pagination nav p {
        margin-top: 10px;
        color: #6b7280;
        font-size: 12px;
    }

    @media (max-width: 700px) {
        .stock-stats {
            grid-template-columns: 1fr;
        }

        .stock-header h1 {
            font-size: 22px;
        }

        .stock-btn {
            width: 100%;
        }

        .stock-stat {
            padding: 16px;
        }

        .stock-stat-value {
            font-size: 23px;
        }

        .stock-panel-header {
            padding: 16px;
        }
    }
</style>

<div class="stock-page">

    {{-- En-tête --}}
    <div class="stock-header">
        <div>
            <h1>Lots et entrées de stock</h1>
            <p class="stock-subtitle">
                Suivez les approvisionnements, les quantités et les dates de péremption.
            </p>
        </div>

        <a class="stock-btn" href="{{ route('lots.create') }}">
            <span>＋</span> Nouvelle entrée de stock
        </a>
    </div>

    {{-- Indicateurs --}}
    @php
        $totalLots = $lots->total();

        $totalQuantity = $lots->getCollection()->sum('quantity');

        $expiredLots = $lots->getCollection()->filter(function ($lot) {
            return $lot->expiry_date &&
                \Illuminate\Support\Carbon::parse($lot->expiry_date)->toDateString() < today()->toDateString();
        })->count();

        $nearExpiryLots = $lots->getCollection()->filter(function ($lot) {
            if (!$lot->expiry_date) {
                return false;
            }

            $expiry = \Illuminate\Support\Carbon::parse($lot->expiry_date);

            return $expiry->toDateString() >= today()->toDateString()
                && $expiry->lte(today()->addDays(30));
        })->count();
    @endphp

    <div class="stock-stats">
        <div class="stock-stat">
            <div class="stock-stat-label">Nombre de lots</div>
            <div class="stock-stat-value">{{ number_format($totalLots, 0, ',', ' ') }}</div>
            <div class="stock-stat-note">Lots enregistrés dans la liste</div>
        </div>

        <div class="stock-stat">
            <div class="stock-stat-label">Quantité restante</div>
            <div class="stock-stat-value">{{ number_format($totalQuantity, 0, ',', ' ') }}</div>
            <div class="stock-stat-note">Quantités des lots de cette page</div>
        </div>

        <div class="stock-stat">
            <div class="stock-stat-label">Lots à surveiller</div>
            <div class="stock-stat-value">{{ $expiredLots + $nearExpiryLots }}</div>
            <div class="stock-stat-note">
                {{ $expiredLots }} périmé(s) · {{ $nearExpiryLots }} proche(s) de la péremption
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="stock-panel">
        <div class="stock-panel-header">
            <h2>Liste des lots</h2>
            <span class="stock-count">
                {{ number_format($lots->count(), 0, ',', ' ') }} affiché(s)
            </span>
        </div>

        <div class="stock-table-wrap">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Médicament</th>
                        <th>N° de lot</th>
                        <th>Fournisseur</th>
                        <th>Quantité restante</th>
                        <th>Prix d'achat / unité</th>
                        php artisan tinker
                        <th>État</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($lots as $lot)
                        @php
                            $expiry = $lot->expiry_date
                                ? \Illuminate\Support\Carbon::parse($lot->expiry_date)
                                : null;

                            $isExpired = $expiry && $expiry->toDateString() < today()->toDateString();

                            $isNearExpiry = $expiry
                                && !$isExpired
                                && $expiry->lte(today()->addDays(30));
                        @endphp

                        <tr>
                            <td>
                                <div class="stock-medicine">
                                    {{ $lot->medicine->name ?? 'Médicament indisponible' }}
                                </div>
                            </td>

                            <td>
                                <span class="stock-lot">
                                    {{ $lot->lot_number ?: 'Non renseigné' }}
                                </span>
                            </td>

                            <td>{{ $lot->supplier ?: 'Non renseigné' }}</td>

                            <td>
                                <span class="stock-quantity">
                                    {{ number_format($lot->quantity, 0, ',', ' ') }}
                                </span>
                            </td>

                            <td class="stock-price">
                                {{ number_format((float) $lot->purchase_price, 0, ',', ' ') }} F CFA
                            </td>



                            <td>
                            
    <span class="stock-badge stock-valid">● Valide</span>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="stock-empty">
                                <div class="stock-empty-icon">📦</div>
                                <strong>Aucune entrée de stock</strong>
                                <p>Les lots ajoutés apparaîtront ici.</p>
                                <a class="stock-btn" href="{{ route('lots.create') }}">
                                    Ajouter le premier lot
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lots->hasPages())
            <div class="stock-pagination">
                {{ $lots->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
