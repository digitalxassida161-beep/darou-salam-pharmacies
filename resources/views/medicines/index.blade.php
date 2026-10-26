@extends('layouts.app')

@section('title', 'Gestion des médicaments')

@section('content')
<div class="medicine-page">

    {{-- En-tête --}}
    <div class="medicine-header">
        <div>
            <div class="medicine-eyebrow">GESTION DE LA PHARMACIE</div>
            <h1>Gestion des médicaments</h1>
            <p>Consultez les produits, surveillez les stocks et gérez votre catalogue.</p>
        </div>

        <a href="{{ route('medicaments.create') }}" class="medicine-add-btn">
            <span class="add-icon">+</span>
            Ajouter un médicament
        </a>
    </div>

    {{-- Indicateurs --}}
    <div class="medicine-stats">
        <div class="medicine-stat">
            <div class="stat-icon stat-icon-green">▤</div>
            <div>
                <div class="stat-label">Médicaments affichés</div>
                <div class="stat-number">{{ $medicines->total() }}</div>
                <div class="stat-description">Dans le catalogue</div>
            </div>
        </div>

        <div class="medicine-stat">
            <div class="stat-icon stat-icon-blue">✓</div>
            <div>
                <div class="stat-label">Produits actifs</div>
                <div class="stat-number">
                    {{ \App\Models\Medicine::where('active', true)->count() }}
                </div>
                <div class="stat-description">Disponibles dans le catalogue</div>
            </div>
        </div>

        <div class="medicine-stat">
            <div class="stat-icon stat-icon-orange">!</div>
            <div>
                <div class="stat-label">Stock à surveiller</div>
                <div class="stat-number">
                    {{ \App\Models\Medicine::where('active', true)
                        ->whereRaw('minimum_stock > 0')
                        ->count() > 0
                        ? $medicines->getCollection()->filter(function ($m) {
                            return $m->active && (int) $m->stock_total <= (int) $m->minimum_stock;
                        })->count()
                        : 0 }}
                </div>
                <div class="stat-description">Parmi les produits de cette page</div>
            </div>
        </div>
    </div>

    {{-- Recherche et filtres côté navigateur --}}
    <div class="medicine-panel">
        <div class="medicine-toolbar">
            <div>
                <h2>Catalogue des médicaments</h2>
                <p>Recherchez un produit ou filtrez selon son état.</p>
            </div>
            <div class="medicine-count">
                {{ $medicines->total() }} produit(s)
            </div>
        </div>

        <div class="medicine-filters">
            <div class="medicine-search">
                <span aria-hidden="true">⌕</span>
                <input type="search" id="medicineSearch"
                       placeholder="Rechercher par nom, forme ou dosage..."
                       aria-label="Rechercher un médicament">
            </div>

            <select id="medicineStatus" class="medicine-select"
                    aria-label="Filtrer par état">
                <option value="all">Tous les états</option>
                <option value="active">Actifs</option>
                <option value="archived">Archivés</option>
            </select>

            <select id="medicineStock" class="medicine-select"
                    aria-label="Filtrer par stock">
                <option value="all">Tous les stocks</option>
                <option value="low">Stock faible</option>
                <option value="ok">Stock suffisant</option>
            </select>
        </div>

        <div class="medicine-table-wrap">
            <table class="medicine-table">
                <thead>
                    <tr>
                        <th>Médicament</th>
                        <th>Présentation</th>
                        <th>Prix de vente</th>
                        <th>Stock valide</th>
                        <th>Seuil minimum</th>
                        <th>État</th>
                        <th class="actions-heading">Actions</th>
                    </tr>
                </thead>

                <tbody id="medicineTableBody">
                    @forelse($medicines as $m)
                        <tr class="medicine-row"
                            data-search="{{ strtolower($m->name.' '.$m->form.' '.$m->strength) }}"
                            data-status="{{ $m->active ? 'active' : 'archived' }}"
                            data-stock="{{ (int) $m->stock_total <= (int) $m->minimum_stock ? 'low' : 'ok' }}">

                            <td>
                                <div class="medicine-name-cell">
                                    <div class="medicine-avatar">
                                        {{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="medicine-name">{{ $m->name }}</div>
                                        <div class="medicine-subtitle">
                                            Réf. MED-{{ str_pad((string) $m->id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="medicine-form">{{ $m->form ?: 'Non précisée' }}</div>
                                <div class="medicine-strength">{{ $m->strength ?: 'Dosage non précisé' }}</div>
                            </td>

                            <td class="medicine-price">
                                {{ number_format((float) $m->sale_price, 0, ',', ' ') }}
                                <span>F CFA</span>
                            </td>

                            <td>
                                <span class="stock-number {{ (int) $m->stock_total <= (int) $m->minimum_stock ? 'stock-low' : 'stock-ok' }}">
                                    {{ number_format((int) $m->stock_total, 0, ',', ' ') }}
                                </span>
                            </td>

                            <td class="threshold-cell">
                                {{ number_format((int) $m->minimum_stock, 0, ',', ' ') }}
                            </td>

                            <td>
                                @if($m->active)
                                    <span class="medicine-status status-active">
                                        <span></span> Actif
                                    </span>
                                @else
                                    <span class="medicine-status status-archived">
                                        <span></span> Archivé
                                    </span>
                                @endif

                                @if($m->active && (int) $m->stock_total <= (int) $m->minimum_stock)
                                    <div class="stock-warning">Stock à réapprovisionner</div>
                                @endif
                            </td>

                            <td>
                                <div class="medicine-actions">
                                    <a class="action-edit"
                                       href="{{ route('medicaments.edit', $m) }}"
                                       title="Modifier ce médicament">
                                        Modifier
                                    </a>

                                    @if($m->active)
                                        <form method="POST"
                                              action="{{ route('medicaments.destroy', $m) }}"
                                              onsubmit="return confirm('Voulez-vous vraiment archiver ce médicament ? Il ne sera plus disponible à la vente, mais son historique sera conservé.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-archive">
                                                Archiver
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="medicine-empty">
                                    <div class="empty-symbol">▤</div>
                                    <h3>Aucun médicament enregistré</h3>
                                    <p>Commencez par ajouter votre premier médicament au catalogue.</p>
                                    <a href="{{ route('medicaments.create') }}" class="medicine-add-btn">
                                        + Ajouter un médicament
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    <tr id="medicineNoResult" style="display:none">
                        <td colspan="7" class="medicine-no-result">
                            Aucun médicament ne correspond à votre recherche.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($medicines->hasPages())
            <div class="medicine-pagination">
                <div class="pagination-info">
                    Affichage de {{ $medicines->firstItem() }} à {{ $medicines->lastItem() }}
                    sur {{ $medicines->total() }} médicament(s)
                </div>
                <div>{{ $medicines->links() }}</div>
            </div>
        @endif
    </div>

    <div class="medicine-footer">
        <span>Darou Salam Santé – Pharmacie</span>
        <span>Gestion sécurisée du catalogue</span>
    </div>
</div>

<style>
    .medicine-page {
        --med-green: #075e54;
        --med-green-dark: #064b43;
        --med-green-soft: #e8f5f1;
        --med-text: #263548;
        --med-muted: #788698;
        --med-border: #e5ebf0;
        color: var(--med-text);
        padding: 8px 0 28px;
    }

    .medicine-page * { box-sizing: border-box; }

    .medicine-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .medicine-eyebrow {
        color: var(--med-green);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        margin-bottom: 7px;
    }

    .medicine-header h1 {
        font-size: clamp(24px, 3vw, 31px);
        line-height: 1.25;
        font-weight: 800;
        letter-spacing: -.7px;
        margin: 0;
    }

    .medicine-header p {
        margin: 8px 0 0;
        color: var(--med-muted);
        font-size: 13px;
    }

    .medicine-add-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        background: var(--med-green);
        color: #fff !important;
        border: 1px solid var(--med-green);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        transition: background .2s, transform .2s;
    }

    .medicine-add-btn:hover {
        background: var(--med-green-dark);
        transform: translateY(-1px);
    }

    .add-icon {
        font-size: 21px;
        line-height: 15px;
    }

    .medicine-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .medicine-stat {
        display: flex;
        align-items: center;
        gap: 15px;
        background: #fff;
        border: 1px solid var(--med-border);
        border-radius: 12px;
        padding: 20px;
        min-width: 0;
        box-shadow: 0 3px 12px rgba(26, 45, 65, .035);
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        font-size: 21px;
        font-weight: 800;
    }

    .stat-icon-green { color: #087767; background: #e8f5f1; }
    .stat-icon-blue { color: #3864ad; background: #edf2ff; }
    .stat-icon-orange { color: #b66b13; background: #fff3e3; }

    .stat-label {
        color: var(--med-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .stat-number {
        color: var(--med-text);
        font-size: 27px;
        font-weight: 800;
        line-height: 1.35;
        margin-top: 3px;
    }

    .stat-description {
        color: #97a2af;
        font-size: 10px;
        margin-top: 2px;
    }

    .medicine-panel {
        background: #fff;
        border: 1px solid var(--med-border);
        border-radius: 13px;
        box-shadow: 0 3px 12px rgba(26, 45, 65, .035);
        overflow: hidden;
    }

    .medicine-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 22px 22px 18px;
    }

    .medicine-toolbar h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
    }

    .medicine-toolbar p {
        margin: 6px 0 0;
        color: var(--med-muted);
        font-size: 12px;
    }

    .medicine-count {
        background: var(--med-green-soft);
        color: var(--med-green);
        padding: 7px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .medicine-filters {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 22px 19px;
        flex-wrap: wrap;
    }

    .medicine-search {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1 1 270px;
        min-width: 0;
        border: 1px solid #dbe3eb;
        border-radius: 8px;
        padding: 0 12px;
        min-height: 42px;
        color: #8a97a6;
    }

    .medicine-search:focus-within {
        border-color: #41998b;
        box-shadow: 0 0 0 3px rgba(7, 94, 84, .08);
    }

    .medicine-search > span { font-size: 23px; }

    .medicine-search input {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--med-text);
        font-size: 12px;
    }

    .medicine-select {
        min-height: 42px;
        border: 1px solid #dbe3eb;
        border-radius: 8px;
        background: #fff;
        color: #526174;
        padding: 0 11px;
        font-size: 12px;
        max-width: 100%;
    }

    .medicine-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .medicine-table {
        width: 100%;
        min-width: 850px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }

    .medicine-table thead th {
        padding: 13px 14px;
        background: #f5f8fa;
        border-top: 1px solid #edf0f4;
        border-bottom: 1px solid #e5ebf0;
        color: #68788c;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    .medicine-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .medicine-table tbody tr:last-child td { border-bottom: 0; }

    .medicine-table tbody tr:hover td { background: #fafcfb; }

    .medicine-name-cell {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 170px;
    }

    .medicine-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--med-green-soft);
        color: var(--med-green);
        font-size: 15px;
        font-weight: 800;
    }

    .medicine-name {
        color: #27384b;
        font-size: 12px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .medicine-subtitle {
        margin-top: 4px;
        color: #96a1af;
        font-size: 10px;
    }

    .medicine-form {
        color: #435267;
        font-weight: 700;
    }

    .medicine-strength {
        color: #929eac;
        font-size: 10px;
        margin-top: 4px;
    }

    .medicine-price {
        color: #183e35;
        font-weight: 800;
        white-space: nowrap;
    }

    .medicine-price span {
        display: block;
        color: #8a97a6;
        font-size: 10px;
        font-weight: 500;
        margin-top: 3px;
    }

    .stock-number {
        display: inline-block;
        min-width: 38px;
        text-align: center;
        border-radius: 6px;
        padding: 6px 9px;
        font-weight: 800;
    }

    .stock-ok { background: #e8f5ed; color: #247346; }
    .stock-low { background: #fff0df; color: #a65b13; }

    .threshold-cell { color: #627185; }

    .medicine-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .medicine-status > span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-active { background: #e8f5ed; color: #247346; }
    .status-archived { background: #eef1f4; color: #6d7784; }

    .stock-warning {
        margin-top: 6px;
        color: #a65b13;
        font-size: 10px;
        font-weight: 700;
    }

    .medicine-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .medicine-actions form { margin: 0; }

    .action-edit,
    .action-archive {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 31px;
        padding: 7px 9px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none !important;
        cursor: pointer;
        white-space: nowrap;
    }

    .action-edit {
        background: #edf3ff;
        color: #355ba1 !important;
        border: 1px solid #dce7ff;
    }

    .action-edit:hover { background: #dfe9ff; }

    .action-archive {
        background: #fff;
        color: #b4534b;
        border: 1px solid #f0d8d5;
    }

    .action-archive:hover { background: #fff1f0; }

    .medicine-empty {
        padding: 45px 15px;
        text-align: center;
    }

    .empty-symbol {
        margin: 0 auto 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        border-radius: 15px;
        background: var(--med-green-soft);
        color: var(--med-green);
        font-size: 26px;
    }

    .medicine-empty h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
    }

    .medicine-empty p {
        margin: 8px 0 18px;
        color: var(--med-muted);
        font-size: 12px;
    }

    .medicine-no-result {
        text-align: center;
        padding: 30px !important;
        color: var(--med-muted);
    }

    .medicine-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        border-top: 1px solid var(--med-border);
        padding: 15px 22px;
    }

    .pagination-info {
        color: var(--med-muted);
        font-size: 11px;
    }

    .medicine-pagination nav p { margin-bottom: 0; }
    .medicine-pagination nav { margin: 0; }

    .medicine-footer {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
        color: #929eac;
        font-size: 10px;
    }

    @media (max-width: 900px) {
        .medicine-stats { grid-template-columns: 1fr; gap: 11px; }
        .medicine-stat { padding: 15px; }
    }

    @media (max-width: 600px) {
        .medicine-header { align-items: stretch; }
        .medicine-header .medicine-add-btn { width: 100%; }
        .medicine-toolbar { padding: 17px 15px; }
        .medicine-filters { padding: 0 15px 16px; }
        .medicine-search, .medicine-select { flex: 1 1 100%; width: 100%; }
        .medicine-pagination { padding: 14px; }
        .medicine-footer { flex-direction: column; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('medicineSearch');
        const statusFilter = document.getElementById('medicineStatus');
        const stockFilter = document.getElementById('medicineStock');
        const rows = Array.from(document.querySelectorAll('.medicine-row'));
        const noResult = document.getElementById('medicineNoResult');

        function filterMedicines() {
            const query = (searchInput.value || '').trim().toLocaleLowerCase();
            const status = statusFilter.value;
            const stock = stockFilter.value;
            let visible = 0;

            rows.forEach(function (row) {
                const matchesSearch = row.dataset.search.includes(query);
                const matchesStatus = status === 'all' || row.dataset.status === status;
                const matchesStock = stock === 'all' || row.dataset.stock === stock;
                const show = matchesSearch && matchesStatus && matchesStock;

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noResult) {
                noResult.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            }
        }

        if (searchInput && statusFilter && stockFilter) {
            searchInput.addEventListener('input', filterMedicines);
            statusFilter.addEventListener('change', filterMedicines);
            stockFilter.addEventListener('change', filterMedicines);
        }
    });
</script>
@endsection