
@extends('layouts.app')

@section('title', 'Gestion des pharmacies')

@section('content')
<div class="licenses-page">

    {{-- En-tête --}}
    <div class="licenses-header">
        <div>
            <div class="licenses-eyebrow">ADMINISTRATION CENTRALE</div>
            <h1>Gestion des pharmacies</h1>
            <p>Gérez les licences, les abonnements et les paiements de vos pharmacies clientes.</p>
        </div>

        <a href="{{ route('admin.licenses.create') }}" class="license-btn license-btn-primary">
            <span class="btn-icon">＋</span>
            Ajouter une pharmacie
        </a>
    </div>

    {{-- Notifications --}}
    @if (session('success'))
        <div class="license-alert license-alert-success">
            <span class="alert-icon">✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="license-alert license-alert-error">
            <div>
                <strong>Une erreur est survenue.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Statistiques --}}
    @php
        $totalLicenses = $licenses->total();
        $activeLicenses = $licenses->getCollection()->where('status', 'active')->count();
        $suspendedLicenses = $licenses->getCollection()->where('status', 'suspended')->count();
        $expiredSubscriptions = $licenses->getCollection()->filter(function ($license) {
            return $license->subscription_expires_at
                && $license->subscription_expires_at->lt(today());
        })->count();
    @endphp

    <div class="license-stats">
        <div class="license-stat-card">
            <div class="stat-top">
                <span class="stat-label">Total des pharmacies</span>
                <span class="stat-icon stat-icon-blue">▤</span>
            </div>
            <div class="stat-number">{{ $totalLicenses }}</div>
            <div class="stat-description">Pharmacies enregistrées</div>
        </div>

        <div class="license-stat-card">
            <div class="stat-top">
                <span class="stat-label">Licences actives</span>
                <span class="stat-icon stat-icon-green">✓</span>
            </div>
            <div class="stat-number">{{ $activeLicenses }}</div>
            <div class="stat-description">Sur la page actuelle</div>
        </div>

        <div class="license-stat-card">
            <div class="stat-top">
                <span class="stat-label">Licences suspendues</span>
                <span class="stat-icon stat-icon-orange">Ⅱ</span>
            </div>
            <div class="stat-number">{{ $suspendedLicenses }}</div>
            <div class="stat-description">Sur la page actuelle</div>
        </div>

        <div class="license-stat-card">
            <div class="stat-top">
                <span class="stat-label">Abonnements expirés</span>
                <span class="stat-icon stat-icon-red">!</span>
            </div>
            <div class="stat-number">{{ $expiredSubscriptions }}</div>
            <div class="stat-description">Sur la page actuelle</div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="license-panel">
        <div class="license-panel-header">
            <div>
                <h2>Répertoire des pharmacies</h2>
                <p>Consultez les licences et gérez les comptes clients.</p>
            </div>

            <span class="license-count">
                {{ $licenses->firstItem() ?? 0 }}–{{ $licenses->lastItem() ?? 0 }}
                sur {{ $licenses->total() }}
            </span>
        </div>

        <div class="license-table-wrap">
            <table class="license-table">
                <thead>
                    <tr>
                        <th>PHARMACIE</th>
                        <th>LICENCE</th>
                        <th>INSTALLATION</th>
                        <th>MAINTENANCE</th>
                        <th>ABONNEMENT</th>
                        <th>STATUT</th>
                        <th class="actions-heading">ACTIONS</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($licenses as $license)
                        <tr>
                            <td>
                                <div class="pharmacy-cell">
                                    <div class="pharmacy-logo">
                                        @if ($license->logo)
                                            <img
                                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($license->logo) }}"
                                                alt="Logo {{ $license->pharmacy_name }}"
                                                onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';"
                                            >
                                            <span class="pharmacy-initial" style="display:none">
                                                {{ mb_strtoupper(mb_substr($license->pharmacy_name, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="pharmacy-initial">
                                                {{ mb_strtoupper(mb_substr($license->pharmacy_name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="pharmacy-details">
                                        <strong>{{ $license->pharmacy_name }}</strong>
                                        <span>{{ $license->pharmacy_phone ?: 'Téléphone non renseigné' }}</span>
                                        @if ($license->pharmacy_address)
                                            <small>{{ $license->pharmacy_address }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <code class="license-key">{{ $license->license_key }}</code>
                            </td>

                            <td>
                                <span class="date-main">
                                    {{ $license->installation_date?->format('d/m/Y') ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if ($license->maintenance_expires_at)
                                    <span class="date-main">
                                        {{ $license->maintenance_expires_at->format('d/m/Y') }}
                                    </span>
                                    @if ($license->maintenance_expires_at->lt(today()))
                                        <span class="date-note date-note-warning">Maintenance expirée</span>
                                    @endif
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>

                            <td>
                                @if ($license->subscription_expires_at)
                                    <span class="date-main">
                                        {{ $license->subscription_expires_at->format('d/m/Y') }}
                                    </span>

                                    @if ($license->subscription_expires_at->lt(today()))
                                        <span class="license-badge badge-expired">Expiré</span>
                                    @elseif ($license->subscription_expires_at->lte(today()->addDays(30)))
                                        <span class="license-badge badge-warning">Expire bientôt</span>
                                    @else
                                        <span class="date-note">À jour</span>
                                    @endif
                                @else
                                    <span class="license-badge badge-warning">Non défini</span>
                                @endif
                            </td>

                            <td>
                                @if ($license->status === 'active')
                                    <span class="license-badge badge-active">
                                        <span class="status-dot"></span> Active
                                    </span>
                                @elseif ($license->status === 'suspended')
                                    <span class="license-badge badge-suspended">
                                        <span class="status-dot"></span> Suspendue
                                    </span>
                                @else
                                    <span class="license-badge badge-expired">
                                        <span class="status-dot"></span> Expirée
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="license-actions">
                                    <a
                                        href="{{ route('admin.licenses.edit', $license) }}"
                                        class="action-link action-edit"
                                        title="Modifier cette pharmacie"
                                    >
                                        Modifier
                                    </a>

                                    <a
                                        href="{{ route('admin.licenses.payments.create', $license) }}"
                                        class="action-link action-payment"
                                        title="Enregistrer un paiement"
                                    >
                                        + Paiement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="license-empty">
                                    <div class="empty-icon">▤</div>
                                    <h3>Aucune pharmacie enregistrée</h3>
                                    <p>Ajoutez votre première pharmacie pour commencer à gérer ses licences.</p>
                                    <a href="{{ route('admin.licenses.create') }}" class="license-btn license-btn-primary">
                                        Ajouter une pharmacie
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($licenses->hasPages())
            <div class="license-pagination">
                {{ $licenses->links() }}
            </div>
        @endif
    </div>

    <div class="license-footer-note">
        <span>Darou Salam Santé</span>
        <span>Administration sécurisée des licences</span>
    </div>
</div>

<style>
    .licenses-page {
        --lp-green: #087f5b;
        --lp-dark: #123b35;
        --lp-muted: #718096;
        color: #172033;
    }

    .licenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .licenses-eyebrow {
        color: #087f5b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        margin-bottom: 8px;
    }

    .licenses-header h1 {
        margin: 0 0 8px;
        font-size: clamp(23px, 3vw, 30px);
        letter-spacing: -.7px;
    }

    .licenses-header p,
    .license-panel-header p {
        margin: 0;
        color: var(--lp-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .license-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 9px;
        padding: 12px 16px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: transform .15s, box-shadow .15s;
    }

    .license-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px #123b351a;
    }

    .license-btn-primary {
        background: #087f5b;
        color: white;
    }

    .license-btn-primary:hover {
        background: #066849;
        color: white;
    }

    .btn-icon { font-size: 20px; line-height: 12px; }

    .license-alert {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .license-alert-success {
        background: #ecfdf3;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .license-alert-error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
    }

    .license-alert ul { margin-bottom: 0; margin-top: 6px; }
    .alert-icon { font-weight: 800; }

    .license-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .license-stat-card {
        background: #fff;
        border: 1px solid #e4eaf0;
        border-radius: 13px;
        padding: 19px;
        box-shadow: 0 3px 12px #14213b06;
        min-width: 0;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 14px;
    }

    .stat-label { color: #64748b; font-size: 12px; font-weight: 600; }

    .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }

    .stat-icon-blue { background: #eff6ff; color: #2563eb; }
    .stat-icon-green { background: #ecfdf3; color: #15803d; }
    .stat-icon-orange { background: #fff7ed; color: #c2410c; }
    .stat-icon-red { background: #fff1f2; color: #be123c; }

    .stat-number {
        font-size: 30px;
        line-height: 1.1;
        font-weight: 800;
        color: #172033;
        letter-spacing: -1px;
    }

    .stat-description {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 8px;
    }

    .license-panel {
        background: #fff;
        border: 1px solid #e1e8ef;
        border-radius: 14px;
        box-shadow: 0 4px 16px #14213b08;
        overflow: hidden;
    }

    .license-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        padding: 22px 22px 18px;
    }

    .license-panel-header h2 {
        margin: 0 0 6px;
        font-size: 17px;
        color: #172033;
    }

    .license-count {
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 7px;
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
    }

    .license-table-wrap { width: 100%; overflow-x: auto; }

    .license-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1050px;
    }

    .license-table thead th {
        background: #f8fafc;
        color: #64748b;
        padding: 13px 15px;
        border-top: 1px solid #edf1f5;
        border-bottom: 1px solid #e5ebf1;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .65px;
        white-space: nowrap;
    }

    .license-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
        font-size: 12px;
        background: #fff;
    }

    .license-table tbody tr:last-child td { border-bottom: 0; }
    .license-table tbody tr:hover td { background: #fbfdfc; }
    .actions-heading { text-align: right; }

    .pharmacy-cell {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 190px;
    }

    .pharmacy-logo {
        width: 43px;
        height: 43px;
        border: 1px solid #e5eaf0;
        border-radius: 10px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }

    .pharmacy-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 3px;
    }

    .pharmacy-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: #087f5b;
        background: #eaf7f1;
        font-weight: 800;
        font-size: 17px;
    }

    .pharmacy-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .pharmacy-details strong {
        color: #1e293b;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .pharmacy-details span,
    .pharmacy-details small {
        color: #7b8798;
        font-size: 10px;
        overflow-wrap: anywhere;
    }

    .license-key {
        display: inline-block;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 5px 7px;
        border-radius: 5px;
        font-size: 10px;
        white-space: nowrap;
    }

    .date-main {
        color: #334155;
        white-space: nowrap;
        font-weight: 600;
    }

    .date-note {
        display: block;
        color: #15803d;
        font-size: 10px;
        margin-top: 5px;
    }

    .date-note-warning { color: #b45309; }

    .license-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-active { background: #dcfce7; color: #166534; }
    .badge-suspended { background: #fef3c7; color: #92400e; }
    .badge-expired { background: #fee2e2; color: #991b1b; }
    .badge-warning { background: #fff7ed; color: #9a3412; }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .license-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 7px;
        min-width: 165px;
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 9px;
        border-radius: 7px;
        text-decoration: none;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
        transition: background .15s;
    }

    .action-edit {
        color: #334155;
        border: 1px solid #dbe3eb;
        background: #fff;
    }

    .action-edit:hover { background: #f1f5f9; color: #172033; }

    .action-payment {
        color: #fff;
        background: #087f5b;
        border: 1px solid #087f5b;
    }

    .action-payment:hover { background: #066849; color: #fff; }

    .license-empty {
        text-align: center;
        padding: 55px 20px;
    }

    .empty-icon {
        font-size: 34px;
        color: #087f5b;
        margin-bottom: 12px;
    }

    .license-empty h3 { margin: 0 0 8px; font-size: 17px; }
    .license-empty p { color: #718096; font-size: 13px; margin-bottom: 20px; }

    .license-pagination { padding: 18px 22px; border-top: 1px solid #edf1f5; }
    .license-pagination nav { justify-content: flex-end; }

    .license-footer-note {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        color: #94a3b8;
        font-size: 11px;
        margin-top: 18px;
        padding: 0 3px;
    }

    .license-footer-note span:first-child {
        font-weight: 700;
        color: #64748b;
    }

    @media (max-width: 1000px) {
        .license-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 600px) {
        .licenses-header { align-items: stretch; }
        .licenses-header .license-btn { width: 100%; }
        .license-stats { gap: 10px; }
        .license-stat-card { padding: 14px; }
        .stat-number { font-size: 25px; }
        .stat-label { font-size: 11px; }
        .license-panel-header { padding: 17px; }
        .license-footer-note { flex-direction: column; }
    }
</style>
@endsection
