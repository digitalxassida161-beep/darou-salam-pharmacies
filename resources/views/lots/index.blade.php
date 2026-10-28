
@extends('layouts.app')

@section('title', 'Lots et entrées de stock')

@section('content')
<style>
    .stock-page {
        --stock-primary: #087f5b;
        --stock-primary-dark: #066649;
        --stock-text: #172b24;
        --stock-muted: #718096;
        --stock-border: #e5ece8;
        color: var(--stock-text);
        padding: 4px 0 24px;
    }

    .stock-page * { box-sizing: border-box; }

    .stock-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .stock-heading { display: flex; align-items: flex-start; gap: 14px; }

    .stock-heading-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #e0f4eb;
        color: var(--stock-primary);
        font-size: 25px;
    }

    .stock-page h1 {
        margin: 0 0 7px;
        font-size: clamp(23px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -.7px;
        color: #18352a;
    }

    .stock-subtitle {
        margin: 0;
        color: var(--stock-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .stock-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 12px 17px;
        border: 1px solid var(--stock-primary);
        border-radius: 11px;
        background: var(--stock-primary);
        color: #fff !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 750;
        box-shadow: 0 5px 14px rgba(8,127,91,.15);
        transition: .18s ease;
        white-space: nowrap;
        cursor: pointer;
    }

    .stock-btn:hover {
        background: var(--stock-primary-dark);
        transform: translateY(-1px);
    }

    .stock-btn-symbol { font-size: 20px; line-height: 1; }

    .stock-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stock-stat {
        display: flex;
        gap: 15px;
        align-items: center;
        min-width: 0;
        padding: 20px;
        background: #fff;
        border: 1px solid var(--stock-border);
        border-radius: 15px;
        box-shadow: 0 5px 18px rgba(24,53,42,.035);
    }

    .stock-stat-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #edf7f2;
        color: #087f5b;
        font-size: 21px;
    }

    .stock-stat-icon.warning {
        background: #fff4d8;
        color: #a16207;
    }

    .stock-stat-label {
        color: var(--stock-muted);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .stock-stat-value {
        color: #18352a;
        font-size: 27px;
        line-height: 1.1;
        font-weight: 850;
    }

    .stock-stat-note {
        color: #9aa8a1;
        font-size: 11px;
        margin-top: 6px;
    }

    .stock-panel {
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--stock-border);
        border-radius: 16px;
        box-shadow: 0 7px 24px rgba(24,53,42,.04);
    }

    .stock-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        padding: 21px 23px;
        border-bottom: 1px solid #edf1ef;
    }

    .stock-panel-title {
        margin: 0 0 5px;
        color: #20392f;
        font-size: 16px;
        font-weight: 800;
    }

    .stock-panel-description {
        margin: 0;
        color: var(--stock-muted);
        font-size: 12px;
    }

    .stock-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 9px;
        background: #e7f6ee;
        color: #087f5b;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .stock-search {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 16px 23px;
        border-bottom: 1px solid #edf1ef;
        background: #fcfdfc;
    }

    .stock-search input {
        flex: 1;
        min-width: 220px;
        padding: 12px 14px;
        border: 1px solid #dce6df;
        border-radius: 10px;
        background: #fff;
        color: #20392f;
        font-size: 13px;
        outline: none;
    }

    .stock-search input:focus {
        border-color: #087f5b;
        box-shadow: 0 0 0 3px rgba(8,127,91,.09);
    }

    .stock-search-help {
        width: 100%;
        color: #718096;
        font-size: 11px;
    }

    .stock-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 16px 23px;
        border-bottom: 1px solid #edf1ef;
        background: #fcfdfc;
    }

    .stock-filter {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border: 1px solid #e1e9e4;
        border-radius: 9px;
        background: #fff;
        color: #596b61;
        text-decoration: none;
        font-size: 12px;
        font-weight: 750;
        transition: .15s ease;
    }

    .stock-filter:hover {
        border-color: #91cbb0;
        color: #087f5b;
    }

    .stock-filter.active {
        background: #087f5b;
        border-color: #087f5b;
        color: #fff;
    }

    .stock-filter-count {
        min-width: 20px;
        padding: 2px 5px;
        border-radius: 6px;
        background: rgba(120,140,128,.12);
        text-align: center;
        font-size: 10px;
    }

    .stock-filter.active .stock-filter-count {
        background: rgba(255,255,255,.2);
    }

    .stock-table-wrap { width: 100%; overflow-x: auto; }

    .stock-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .stock-table thead th {
        padding: 14px 18px;
        background: #f7faf8;
        color: #78877f;
        border-bottom: 1px solid #e9efeb;
        text-align: left;
        white-space: nowrap;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: .65px;
        font-weight: 850;
    }

    .stock-table tbody td {
        padding: 17px 18px;
        border-bottom: 1px solid #f0f3f1;
        color: #53645b;
        font-size: 12px;
        vertical-align: middle;
    }

    .stock-table tbody tr:hover { background: #f8fbf9; }
    .stock-table tbody tr:last-child td { border-bottom: 0; }

    .stock-medicine {
        color: #20392f;
        font-size: 13px;
        font-weight: 800;
    }

    .stock-medicine-caption {
        margin-top: 4px;
        color: #9aa8a1;
        font-size: 11px;
    }

    .stock-lot {
        display: inline-block;
        padding: 6px 9px;
        border: 1px solid #e3ebe6;
        border-radius: 7px;
        background: #f7faf8;
        color: #43564b;
        font-size: 11px;
        font-weight: 750;
    }

    .stock-quantity {
        color: #20392f;
        font-size: 13px;
        font-weight: 850;
    }

    .stock-price {
        color: #20392f !important;
        font-weight: 750;
        white-space: nowrap;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 850;
        white-space: nowrap;
    }

    .stock-badge-dot {
        width: 6px;
        height: 6px;
        display: inline-block;
        border-radius: 50%;
        background: currentColor;
    }

    .stock-valid { background: #e5f7ed; color: #177245; }
    .stock-expired { background: #ffebeb; color: #b42318; }
    .stock-warning { background: #fff3d6; color: #9a6700; }
    .stock-empty {
        text-align: center;
        padding: 55px 20px !important;
        color: #718096 !important;
    }

    .stock-empty-icon {
        display: grid;
        place-items: center;
        width: 56px;
        height: 56px;
        margin: 0 auto 13px;
        border-radius: 16px;
        background: #edf7f2;
        color: #087f5b;
        font-size: 25px;
    }

    .stock-empty strong {
        display: block;
        margin-bottom: 6px;
        color: #20392f;
        font-size: 14px;
    }

    .stock-empty p { margin: 0 0 17px; }
    .stock-pagination { padding: 18px 22px; border-top: 1px solid #edf1ef; }

    .stock-alert {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fff1f2;
        color: #9f1239;
        font-size: 13px;
    }

    @media (max-width: 850px) {
        .stock-stats { grid-template-columns: 1fr; gap: 12px; }
        .stock-stat { padding: 16px; }
        .stock-stat-value { font-size: 24px; }
    }

    @media (max-width: 600px) {
        .stock-hero { align-items: stretch; }
        .stock-heading { gap: 11px; }
        .stock-heading-icon {
            width: 44px;
            height: 44px;
            flex-basis: 44px;
            border-radius: 12px;
        }
        .stock-hero > .stock-btn { width: 100%; }
        .stock-search { padding: 14px; }
        .stock-search input { min-width: 100%; }
        .stock-search .stock-btn { width: 100%; }
        .stock-panel-header { padding: 17px; }
        .stock-filters { padding: 14px; }
        .stock-pagination { padding: 14px; }
    }
</style>

@php
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';

    $currentEtat = request('etat', 'tous');
    $search = request('search', '');

    $today = today();
    $collectionPage = $lots->getCollection();

    $totalLots = $lots->total();
    $pageQuantity = $collectionPage->sum('quantity');

    $expiredLots = $collectionPage->filter(function ($lot) use ($today) {
        return $lot->expiry_date
            && \Illuminate\Support\Carbon::parse($lot->expiry_date)->toDateString()
                < $today->toDateString();
    })->count();

    $nearExpiryLots = $collectionPage->filter(function ($lot) use ($today) {
        if (!$lot->expiry_date) {
            return false;
        }

        $expiry = \Illuminate\Support\Carbon::parse($lot->expiry_date);

        return $expiry->toDateString() >= $today->toDateString()
            && $expiry->lte($today->copy()->addDays(30));
    })->count();

    $countValid = $collectionPage->filter(function ($lot) use ($today) {
        if (!$lot->expiry_date) {
            return false;
        }

        $expiry = \Illuminate\Support\Carbon::parse($lot->expiry_date);

        return $expiry->toDateString() >= $today->toDateString()
            && $expiry->gt($today->copy()->addDays(30));
    })->count();

    $countExpired = $collectionPage->filter(function ($lot) use ($today) {
        return $lot->expiry_date
            && \Illuminate\Support\Carbon::parse($lot->expiry_date)->toDateString()
                < $today->toDateString();
    })->count();

    $countNear = $collectionPage->filter(function ($lot) use ($today) {
        if (!$lot->expiry_date) {
            return false;
        }

        $expiry = \Illuminate\Support\Carbon::parse($lot->expiry_date);

        return $expiry->toDateString() >= $today->toDateString()
            && $expiry->lte($today->copy()->addDays(30));
    })->count();

    $countMissing = $collectionPage->filter(fn ($lot) => !$lot->expiry_date)->count();
@endphp

<div class="stock-page">
    <div class="stock-hero">
        <div class="stock-heading">
            <div class="stock-heading-icon" aria-hidden="true">▦</div>
            <div>
                <h1>Gestion des lots</h1>
                <p class="stock-subtitle">
                    Recherchez un médicament, vérifiez les quantités et surveillez les péremptions.
                </p>
            </div>
        </div>

        @if($isAdmin)
            <a class="stock-btn" href="{{ route('lots.create') }}">
                <span class="stock-btn-symbol">＋</span>
                Nouvelle entrée de stock
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="stock-alert"
             style="border-color:#bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="stock-alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="stock-stats">
        <div class="stock-stat">
            <div class="stock-stat-icon" aria-hidden="true">▤</div>
            <div>
                <div class="stock-stat-label">Nombre de lots trouvés</div>
                <div class="stock-stat-value">
                    {{ number_format($totalLots, 0, ',', ' ') }}
                </div>
                <div class="stock-stat-note">
                    @if($search !== '')
                        Résultats de la recherche
                    @else
                        Lots correspondant aux filtres
                    @endif
                </div>
            </div>
        </div>

        <div class="stock-stat">
            <div class="stock-stat-icon" aria-hidden="true">▣</div>
            <div>
                <div class="stock-stat-label">Quantité sur cette page</div>
                <div class="stock-stat-value">
                    {{ number_format($pageQuantity, 0, ',', ' ') }}
                </div>
                <div class="stock-stat-note">Unités des lots affichés sur cette page</div>
            </div>
        </div>

        <div class="stock-stat">
            <div class="stock-stat-icon warning" aria-hidden="true">!</div>
            <div>
                <div class="stock-stat-label">Lots à surveiller</div>
                <div class="stock-stat-value">
                    {{ number_format($expiredLots + $nearExpiryLots, 0, ',', ' ') }}
                </div>
                <div class="stock-stat-note">
                    {{ $expiredLots }} périmé(s) ·
                    {{ $nearExpiryLots }} proche(s) de la péremption
                </div>
            </div>
        </div>
    </div>

    <section class="stock-panel">
        <div class="stock-panel-header">
            <div>
                <h2 class="stock-panel-title">Inventaire des lots</h2>
                <p class="stock-panel-description">
                    Recherchez un comprimé ou filtrez les lots selon leur état.
                </p>
            </div>

            <span class="stock-count">
                {{ number_format($lots->count(), 0, ',', ' ') }} sur cette page
            </span>
        </div>

        {{-- Recherche d'un médicament --}}
        <form method="GET" action="{{ route('lots.index') }}" class="stock-search">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Nom du médicament, ex. Paracétamol..."
                aria-label="Rechercher un médicament"
            >

            <input type="hidden" name="etat" value="{{ $currentEtat }}">

            <button type="submit" class="stock-btn">
                Rechercher
            </button>

            @if($search !== '')
                <a class="stock-filter" href="{{ route('lots.index', ['etat' => $currentEtat]) }}">
                    Effacer la recherche
                </a>
            @endif

            <div class="stock-search-help">
                Saisissez le nom complet ou une partie du nom du médicament.
            </div>
        </form>

        {{-- Filtres par état --}}
        <nav class="stock-filters" aria-label="Filtrer les lots par état">
            @php
                $filters = [
                    'tous' => 'Tous les lots',
                    'valide' => 'Valides',
                    'bientot' => 'Expire bientôt',
                    'perime' => 'Périmés',
                    'sans_date' => 'Date manquante',
                ];
            @endphp

            @foreach($filters as $key => $label)
                <a
                    class="stock-filter {{ $currentEtat === $key ? 'active' : '' }}"
                    href="{{ route('lots.index', array_filter([
                        'etat' => $key,
                        'search' => $search !== '' ? $search : null,
                    ], fn ($value) => $value !== null)) }}"
                >
                    {{ $label }}

                    <span class="stock-filter-count">
                        @switch($key)
                            @case('tous')
                                {{ $lots->total() }}
                                @break
                            @case('valide')
                                {{ $countValid }}
                                @break
                            @case('bientot')
                                {{ $countNear }}
                                @break
                            @case('perime')
                                {{ $countExpired }}
                                @break
                            @case('sans_date')
                                {{ $countMissing }}
                                @break
                        @endswitch
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="stock-table-wrap">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Médicament</th>
                        <th>N° de lot</th>
                        <th>Fournisseur</th>
                        <th>Quantité restante</th>
                        <th>Prix d'achat / unité</th>
                        <th>Date de péremption</th>
                        <th>État</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($collectionPage as $lot)
                        @php
                            $expiry = $lot->expiry_date
                                ? \Illuminate\Support\Carbon::parse($lot->expiry_date)
                                : null;

                            $isExpired = $expiry
                                && $expiry->toDateString() < $today->toDateString();

                            $isNearExpiry = $expiry
                                && !$isExpired
                                && $expiry->lte($today->copy()->addDays(30));

                            $isOutOfStock = (int) $lot->quantity <= 0;
                        @endphp

                        <tr>
                            <td>
                                <div class="stock-medicine">
                                    {{ $lot->medicine->name ?? 'Médicament indisponible' }}
                                </div>
                                <div class="stock-medicine-caption">
                                    Référence de stock
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

                                @if($isOutOfStock)
                                    <div>
                                        <span class="stock-badge stock-expired">
                                            Rupture de stock
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="stock-price">
                                {{ number_format((float) $lot->purchase_price, 0, ',', ' ') }}
                                F CFA
                            </td>

                            <td>
                                @if($expiry)
                                    <span style="{{ $isExpired
                                        ? 'color:#b42318;font-weight:800;'
                                        : 'color:#43564b;font-weight:700;' }}">
                                        {{ $expiry->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span style="color:#9aa8a1;">
                                        Non renseignée
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($isOutOfStock)
                                    <span class="stock-badge stock-expired">
                                        <span class="stock-badge-dot"></span>
                                        Rupture
                                    </span>
                                @elseif($isExpired)
                                    <span class="stock-badge stock-expired">
                                        <span class="stock-badge-dot"></span>
                                        Périmé
                                    </span>
                                @elseif($isNearExpiry)
                                    <span class="stock-badge stock-warning">
                                        <span class="stock-badge-dot"></span>
                                        Expire bientôt
                                    </span>
                                @elseif(!$expiry)
                                    <span class="stock-badge stock-warning">
                                        <span class="stock-badge-dot"></span>
                                        Date manquante
                                    </span>
                                @else
                                    <span class="stock-badge stock-valid">
                                        <span class="stock-badge-dot"></span>
                                        Valide
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="stock-empty">
                                <div class="stock-empty-icon" aria-hidden="true">▦</div>

                                <strong>
                                    @if($search !== '')
                                        Aucun lot trouvé pour « {{ $search }} »
                                    @else
                                        Aucun lot à afficher
                                    @endif
                                </strong>

                                <p>
                                    Vérifiez l'orthographe du médicament ou essayez un autre filtre.
                                </p>

                                @if($search !== '' || $currentEtat !== 'tous')
                                    <a class="stock-filter"
                                       href="{{ route('lots.index') }}">
                                        Réinitialiser la recherche et les filtres
                                    </a>
                                @elseif($isAdmin)
                                    <a class="stock-btn" href="{{ route('lots.create') }}">
                                        <span class="stock-btn-symbol">＋</span>
                                        Ajouter le premier lot
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lots->hasPages())
            <div class="stock-pagination">
                {{ $lots->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
