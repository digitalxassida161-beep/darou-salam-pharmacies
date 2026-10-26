 @extends('layouts.app')

@section('content')
<div class="sales-report">

    {{-- En-tête du rapport --}}
    <div class="report-header">
        <div>
            <div class="report-eyebrow">GESTION FINANCIÈRE</div>
            <h1>Rapports des ventes</h1>
            <p>Analysez les recettes et suivez l'activité de votre pharmacie.</p>
        </div>

        <button type="button" class="btn-print" onclick="window.print()">
            <span aria-hidden="true">▤</span> Imprimer le rapport
        </button>
    </div>

    {{-- Filtres --}}
    <div class="report-panel filter-panel">
        <form method="GET" action="{{ route('reports.sales') }}">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="period">Période du rapport</label>
                    <select name="period" id="period" class="form-control"
                            onchange="toggleReportDates()">
                        <option value="day" {{ $period === 'day' ? 'selected' : '' }}>
                            Aujourd'hui
                        </option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>
                            Ce mois
                        </option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>
                            Cette année
                        </option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>
                            Période personnalisée
                        </option>
                    </select>
                </div>

                <div id="report-dates"
                     class="filter-dates"
                     style="{{ $period === 'custom' ? '' : 'display:none' }}">

                    <div class="filter-field">
                        <label for="start_date">Date de début</label>
                        <input type="date" name="start_date" id="start_date"
                               class="form-control"
                               value="{{ request('start_date', $start->toDateString()) }}">
                    </div>

                    <div class="filter-field">
                        <label for="end_date">Date de fin</label>
                        <input type="date" name="end_date" id="end_date"
                               class="form-control"
                               value="{{ request('end_date', $end->toDateString()) }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply">
                        Afficher
                    </button>
                    <a href="{{ route('reports.sales') }}" class="btn-reset">
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        @if ($errors->any())
            <div class="report-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="period-caption">
            <span class="period-dot"></span>
            Rapport du <strong>{{ $start->format('d/m/Y') }}</strong>
            au <strong>{{ $end->format('d/m/Y') }}</strong>
        </div>
    </div>

    {{-- Indicateurs principaux --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-top">
                <span class="summary-label">Nombre de ventes</span>
                <span class="summary-icon icon-sales">▤</span>
            </div>
            <div class="summary-value">
                {{ number_format($totalSales, 0, ',', ' ') }}
            </div>
            <div class="summary-note">Transactions enregistrées</div>
        </div>

        <div class="summary-card summary-card-primary">
            <div class="summary-top">
                <span class="summary-label">Chiffre d'affaires</span>
                <span class="summary-icon icon-revenue">₣</span>
            </div>
            <div class="summary-value">
                {{ number_format($totalAmount, 0, ',', ' ') }}
                <span class="currency">F CFA</span>
            </div>
            <div class="summary-note">Montant total des ventes</div>
        </div>

        <div class="summary-card">
            <div class="summary-top">
                <span class="summary-label">Période sélectionnée</span>
                <span class="summary-icon icon-period">◷</span>
            </div>
            <div class="summary-value period-value">
                @switch($period)
                    @case('day') Aujourd'hui @break
                    @case('month') Ce mois @break
                    @case('year') Cette année @break
                    @case('custom') Personnalisée @break
                    @default {{ ucfirst($period) }}
                @endswitch
            </div>
            <div class="summary-note">Période d'analyse</div>
        </div>
    </div>

    {{-- Paiements et récapitulatif journalier --}}
    <div class="report-columns">

        <section class="report-panel">
            <div class="section-heading">
                <div>
                    <h2>Répartition des paiements</h2>
                    <p>Recettes selon le mode de règlement</p>
                </div>
                <span class="section-symbol">₣</span>
            </div>

            <div class="table-responsive">
                <table class="professional-table">
                    <thead>
                        <tr>
                            <th>Moyen de paiement</th>
                            <th class="text-right">Montant encaissé</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentBreakdown as $payment)
                            <tr>
                                <td>
                                    <span class="payment-indicator"></span>
                                    @switch($payment->payment_method)
                                        @case('espèces') Espèces @break
                                        @case('wave') Wave @break
                                        @case('orange_money') Orange Money @break
                                        @case('autre') Autre @break
                                        @default {{ $payment->payment_method ?: 'Non renseigné' }}
                                    @endswitch
                                </td>
                                <td class="text-right amount-cell">
                                    {{ number_format($payment->total, 0, ',', ' ') }}
                                    <small>F CFA</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="empty-state">
                                    Aucun paiement pour cette période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="report-panel">
            <div class="section-heading">
                <div>
                    <h2>Activité journalière</h2>
                    <p>Évolution des ventes par jour</p>
                </div>
                <span class="section-symbol">▥</span>
            </div>

            <div class="table-responsive">
                <table class="professional-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Ventes</th>
                            <th class="text-right">Recettes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dailyTotals as $daily)
                            <tr>
                                <td>
                                    {{ \Illuminate\Support\Carbon::parse($daily->sale_date)->format('d/m/Y') }}
                                </td>
                                <td class="text-right">
                                    {{ number_format($daily->number_of_sales, 0, ',', ' ') }}
                                </td>
                                <td class="text-right amount-cell">
                                    {{ number_format($daily->total, 0, ',', ' ') }}
                                    <small>F CFA</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-state">
                                    Aucune vente pour cette période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    {{-- Historique détaillé --}}
    <section class="report-panel sales-history">
        <div class="section-heading history-heading">
            <div>
                <h2>Historique détaillé des ventes</h2>
                <p>Liste des transactions de la période sélectionnée</p>
            </div>
            <span class="record-count">
                {{ number_format($sales->count(), 0, ',', ' ') }} transaction(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="professional-table history-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date et heure</th>
                        <th>Caissier</th>
                        <th>Moyen de paiement</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>
                                <span class="sale-reference">
                                    {{ $sale->receipt_number ?? $sale->id }}
                                </span>
                            </td>
                            <td>{{ $sale->created_at->format('d/m/Y à H:i') }}</td>
                            <td>{{ $sale->user->name ?? 'Non renseigné' }}</td>
                            <td>
                                @switch($sale->payment_method)
                                    @case('espèces')
                                        <span class="payment-badge badge-cash">Espèces</span>
                                        @break
                                    @case('wave')
                                        <span class="payment-badge badge-wave">Wave</span>
                                        @break
                                    @case('orange_money')
                                        <span class="payment-badge badge-orange">Orange Money</span>
                                        @break
                                    @case('autre')
                                        <span class="payment-badge">Autre</span>
                                        @break
                                    @default
                                        <span class="payment-badge">
                                            {{ $sale->payment_method ?: 'Non renseigné' }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="text-right amount-cell">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }}
                                <small>F CFA</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-title">Aucune vente enregistrée</div>
                                <div>Les ventes de cette période apparaîtront ici.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">CHIFFRE D'AFFAIRES TOTAL</td>
                        <td class="text-right total-footer">
                            {{ number_format($totalAmount, 0, ',', ' ') }} F CFA
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <div class="report-footer">
        <span>{{ config('app.name', 'Darou Salam Santé – Pharmacie') }}</span>
        <span>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</span>
    </div>
</div>

<style>
    .sales-report {
        --report-green: #075e54;
        --report-green-dark: #064b43;
        --report-green-light: #e8f5f1;
        --report-text: #243447;
        --report-muted: #718096;
        --report-border: #e5eaf0;
        color: var(--report-text);
        padding: 8px 0 28px;
    }

    .sales-report * {
        box-sizing: border-box;
    }

    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 26px;
    }

    .report-eyebrow {
        color: var(--report-green);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        margin-bottom: 7px;
    }

    .report-header h1 {
        margin: 0;
        font-size: clamp(25px, 3vw, 32px);
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .report-header p,
    .section-heading p {
        color: var(--report-muted);
        font-size: 13px;
        margin: 8px 0 0;
    }

    .btn-print,
    .btn-apply,
    .btn-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-print {
        background: #fff;
        color: var(--report-green);
        border: 1px solid #cbded9;
    }

    .btn-print:hover {
        background: var(--report-green-light);
    }

    .report-panel {
        background: #fff;
        border: 1px solid var(--report-border);
        border-radius: 13px;
        padding: 22px;
        margin-bottom: 22px;
        box-shadow: 0 3px 12px rgba(26, 45, 65, .035);
        min-width: 0;
    }

    .filter-panel {
        padding: 20px 22px 15px;
    }

    .filter-grid {
        display: flex;
        align-items: flex-end;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 150px;
        min-width: 0;
    }

    .filter-field label {
        font-size: 12px;
        font-weight: 700;
        color: #435267;
        margin-bottom: 7px;
    }

    .sales-report .form-control {
        width: 100%;
        min-height: 42px;
        border: 1px solid #dbe3eb;
        border-radius: 7px;
        background-color: #fff;
        color: var(--report-text);
        padding: 9px 11px;
        font-size: 13px;
        box-shadow: none;
    }

    .sales-report .form-control:focus {
        border-color: #41998b;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(7, 94, 84, .09);
    }

    .filter-dates {
        display: flex;
        flex: 2 1 310px;
        gap: 12px;
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .btn-apply {
        color: #fff;
        background: var(--report-green);
        border: 1px solid var(--report-green);
    }

    .btn-apply:hover {
        color: #fff;
        background: var(--report-green-dark);
    }

    .btn-reset {
        color: #526174;
        background: #fff;
        border: 1px solid #dbe3eb;
    }

    .btn-reset:hover {
        color: var(--report-green);
        background: #f7faf9;
    }

    .period-caption {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
        color: var(--report-muted);
        font-size: 12px;
        border-top: 1px solid #edf0f4;
        margin-top: 17px;
        padding-top: 14px;
    }

    .period-caption strong {
        color: var(--report-text);
    }

    .period-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #16a085;
        margin-right: 5px;
    }

    .report-error {
        color: #a61b1b;
        background: #fff1f0;
        border: 1px solid #ffd4d0;
        padding: 10px 12px;
        border-radius: 7px;
        margin-top: 14px;
        font-size: 13px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .summary-card {
        min-width: 0;
        background: #fff;
        border: 1px solid var(--report-border);
        border-radius: 13px;
        padding: 22px;
        box-shadow: 0 3px 12px rgba(26, 45, 65, .035);
    }

    .summary-card-primary {
        background: linear-gradient(135deg, #075e54 0%, #087d6b 100%);
        color: #fff;
        border-color: transparent;
    }

    .summary-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--report-muted);
    }

    .summary-card-primary .summary-label,
    .summary-card-primary .summary-note {
        color: rgba(255, 255, 255, .8);
    }

    .summary-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 19px;
        font-weight: 800;
    }

    .icon-sales {
        background: #edf2ff;
        color: #4263a8;
    }

    .icon-revenue {
        background: rgba(255, 255, 255, .16);
        color: #fff;
    }

    .icon-period {
        background: #e8f5f1;
        color: var(--report-green);
    }

    .summary-value {
        font-size: clamp(23px, 2.3vw, 31px);
        font-weight: 800;
        letter-spacing: -.7px;
        line-height: 1.35;
        margin-top: 18px;
        overflow-wrap: anywhere;
    }

    .summary-value .currency {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .summary-value.period-value {
        font-size: 23px;
    }

    .summary-note {
        color: var(--report-muted);
        font-size: 11px;
        margin-top: 7px;
    }

    .report-columns {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: start;
    }

    .report-columns .report-panel {
        margin-bottom: 22px;
        padding: 20px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 19px;
    }

    .section-heading h2 {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        color: var(--report-text);
    }

    .section-heading p {
        font-size: 12px;
        margin-top: 5px;
    }

    .section-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 10px;
        color: var(--report-green);
        background: var(--report-green-light);
        font-size: 18px;
        font-weight: 800;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .professional-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        margin: 0;
    }

    .professional-table thead th {
        background: #f5f8fa;
        color: #627185;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .45px;
        padding: 12px 11px;
        border-top: 1px solid #edf0f4;
        border-bottom: 1px solid #e6ebf0;
        white-space: nowrap;
    }

    .professional-table thead th:first-child {
        border-radius: 7px 0 0 0;
    }

    .professional-table thead th:last-child {
        border-radius: 0 7px 0 0;
    }

    .professional-table tbody td {
        padding: 14px 11px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
        color: #39485b;
    }

    .professional-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .professional-table tbody tr:hover td {
        background: #fafcfb;
    }

    .text-right {
        text-align: right !important;
    }

    .amount-cell {
        font-weight: 800;
        color: #173d35 !important;
        white-space: nowrap;
    }

    .amount-cell small {
        color: #8491a0;
        font-size: 10px;
        font-weight: 500;
    }

    .payment-indicator {
        display: inline-block;
        width: 7px;
        height: 7px;
        background: #0e9f83;
        border-radius: 50%;
        margin-right: 7px;
    }

    .empty-state {
        text-align: center;
        color: #8793a2 !important;
        padding: 28px 12px !important;
        line-height: 1.7;
    }

    .empty-title {
        font-size: 13px;
        font-weight: 800;
        color: #526174;
    }

    .history-heading {
        margin-bottom: 20px;
    }

    .record-count {
        background: #edf7f4;
        color: var(--report-green);
        border-radius: 20px;
        padding: 7px 11px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .history-table {
        min-width: 650px;
    }

    .sale-reference {
        color: var(--report-green);
        font-weight: 800;
        white-space: nowrap;
    }

    .payment-badge {
        display: inline-block;
        background: #eef2f6;
        color: #526174;
        border-radius: 5px;
        padding: 5px 8px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-cash {
        background: #e8f5ed;
        color: #247346;
    }

    .badge-wave {
        background: #e8efff;
        color: #3456a6;
    }

    .badge-orange {
        background: #fff0df;
        color: #a65b13;
    }

    .history-table tfoot td {
        padding: 16px 11px;
        background: #f1f7f5;
        border-top: 1px solid #dceae5;
        font-size: 11px;
        font-weight: 800;
        color: var(--report-green-dark);
    }

    .total-footer {
        white-space: nowrap;
        font-size: 13px !important;
    }

    .report-footer {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 3px 2px;
        color: #8a96a5;
        font-size: 10px;
    }

    @media (max-width: 900px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-columns {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }

    @media (max-width: 600px) {
        .report-header {
            align-items: flex-start;
        }

        .btn-print {
            width: 100%;
        }

        .summary-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .summary-card {
            padding: 18px;
        }

        .report-panel {
            padding: 15px;
        }

        .filter-panel {
            padding: 16px;
        }

        .filter-grid,
        .filter-dates {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-field,
        .filter-dates,
        .filter-actions {
            width: 100%;
        }

        .filter-actions > * {
            flex: 1;
        }

        .report-footer {
            flex-direction: column;
        }
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            background: #fff !important;
            color: #111 !important;
            font-size: 10px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        nav,
        header,
        footer,
        .filter-panel,
        .btn-print,
        .report-footer,
        .sales-report > .report-header .btn-print {
            display: none !important;
        }

        .sales-report {
            padding: 0;
        }

        .report-header {
            margin-bottom: 14px;
        }

        .report-header h1 {
            font-size: 23px;
        }

        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .summary-card,
        .report-panel {
            box-shadow: none !important;
            break-inside: avoid;
        }

        .summary-card {
            padding: 13px;
        }

        .summary-value {
            font-size: 21px;
        }

        .report-columns {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .report-panel,
        .report-columns .report-panel {
            padding: 12px;
            margin-bottom: 12px;
        }

        .professional-table {
            font-size: 9px;
        }

        .professional-table thead th,
        .professional-table tbody td,
        .history-table tfoot td {
            padding: 7px;
        }

        .history-table {
            min-width: 0;
        }

        .sales-report .table-responsive {
            overflow: visible !important;
        }

        .professional-table tr {
            break-inside: avoid;
        }
    }
</style>

<script>
    function toggleReportDates() {
        const period = document.getElementById('period');
        const dates = document.getElementById('report-dates');

        if (period && dates) {
            dates.style.display = period.value === 'custom' ? 'flex' : 'none';
        }
    }
</script>
@endsection