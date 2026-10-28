
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Tableau de bord') —
        @auth
            @if (auth()->user()->role === 'super_admin')
                Administration centrale
            @else
                {{ auth()->user()->license?->pharmacy_name ?? 'Pharmacie' }}
            @endif
        @else
            Gestion de pharmacie
        @endauth
    </title>

    <style>
        :root {
            font-family: Arial, sans-serif;
            color: #172033;
            background: #f4f7fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
        }

        header {
            background: #075e54;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            box-shadow: 0 3px 10px #00000012;
        }

        .brand { flex-shrink: 0; }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
        }

        .brand-logo {
            width: 55px;
            height: 55px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 3px;
        }

        .brand-text {
            display: block;
            font-size: 19px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .brand-text small {
            display: block;
            margin-top: 4px;
            font-size: 13px;
            font-weight: normal;
            opacity: .85;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        nav > a {
            color: #fff;
            text-decoration: none;
            padding: 9px 11px;
            border-radius: 7px;
            font-size: 13px;
            white-space: nowrap;
        }

        nav > a:hover { background: #087f70; }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 7px 9px;
            background: #fff;
            border: 1px solid #e5ebf0;
            border-radius: 12px;
            color: #263548;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }

        .user-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #e8f5f1;
            color: #075e54;
        }

        .user-avatar svg {
            width: 23px;
            height: 23px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .user-name {
            color: #263548;
            font-size: 12px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .user-role {
            color: #788698;
            font-size: 11px;
        }

        .logout-form { margin: 0; }

        .logout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 11px;
            border: 1px solid #f0d8d5;
            border-radius: 8px;
            background: #fff5f4;
            color: #b4534b;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
        }

        .logout-button svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        .logout-button:hover {
            background: #b4534b;
            color: #fff;
        }

        main {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 18px 30px;
        }

        h1, h2, h3 { color: #123b35; }

        h1 {
            font-size: 26px;
            margin-top: 0;
        }

        h2 { font-size: 21px; }

        .card {
            background: #fff;
            border: 1px solid #dce4ee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px #1522380a;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            margin: 5px 0 14px;
            background: #fff;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: 2px solid #a7d8ce;
            border-color: #087f5b;
        }

        button, .btn {
            background: #087f5b;
            color: #fff;
            border: 0;
            border-radius: 7px;
            padding: 10px 15px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
        }

        button:hover, .btn:hover { background: #066849; }

        .btn-secondary { background: #475569; }
        .btn-secondary:hover { background: #334155; }

        .danger { color: #b91c1c; }

        .alert, .error {
            padding: 13px 16px;
            margin-bottom: 16px;
            border-radius: 8px;
        }

        .alert {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .muted { color: #64748b; }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 18px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f0f7f5;
            color: #075e54;
            font-size: 13px;
        }

        tr:hover td { background: #fafdfc; }
        .inline { display: inline; }

        footer {
            text-align: center;
            padding: 18px;
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 1000px) {
            header { align-items: flex-start; }
            nav { justify-content: flex-start; }
        }

        @media (max-width: 700px) {
            header { padding: 15px; }
            nav { width: 100%; }
            nav > a { padding: 8px; }
            .user-menu { max-width: 100%; flex-wrap: wrap; }
            main { margin-top: 20px; padding: 0 12px 24px; }
            h1 { font-size: 22px; }
        }

        @media (max-width: 420px) {
            .brand-logo { width: 45px; height: 45px; }
            .brand-text { font-size: 16px; }
            .user-menu { gap: 8px; }
            .logout-button { padding: 9px; }
            .logout-button span { display: none; }
        }

        @media print {
            body {
                background: #fff;
                color: #000;
                font-size: 12px;
            }

            header, footer, .no-print {
                display: none !important;
            }

            main {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            table { font-size: 11px; }
            th, td { padding: 7px; }
            h1, h2, h3 { color: #000; }
        }
    </style>
</head>

<body>
    @php
        $user = auth()->user();
        $pharmacy = $user?->license;
        $isSuperAdmin = $user && $user->role === 'super_admin';

        $appName = $isSuperAdmin
            ? 'Gestion centrale des pharmacies'
            : ($pharmacy?->pharmacy_name ?? 'Pharmacie non configurée');

        /*
         * Chaque pharmacie utilise son propre logo si celui-ci est enregistré.
         * Sinon, on conserve le logo par défaut.
         */
        if ($isSuperAdmin) {
            $logoUrl = asset('images/logo.png');
        } elseif ($pharmacy?->logo) {
            $logoUrl = \Illuminate\Support\Facades\Storage::disk('public')
                ->url($pharmacy->logo);
        } elseif ($pharmacy?->id === 1) {
            $logoUrl = asset('images/logo-comico4.png');
        } else {
            $logoUrl = asset('images/logo.png');
        }
    @endphp

    <header>
        <div class="brand">
            @if ($user)
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <img
                        src="{{ $logoUrl }}"
                        alt="Logo {{ $appName }}"
                        class="brand-logo"
                        onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';"
                    >

                    <span class="brand-text">
                        {{ $appName }}

                        <small>
                            @if ($isSuperAdmin)
                                Administration centrale
                            @else
                                {{ $pharmacy?->pharmacy_address ?? 'Gestion de la pharmacie' }}
                            @endif
                        </small>
                    </span>
                </a>
            @else
                <div class="brand-link">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Logo de l'application"
                        class="brand-logo"
                        onerror="this.style.display='none'"
                    >

                    <span class="brand-text">
                        Gestion de pharmacie
                        <small>Connexion sécurisée</small>
                    </span>
                </div>
            @endif
        </div>

        @auth
            <nav>
                <a href="{{ route('dashboard') }}">Tableau de bord</a>

                @if (!$isSuperAdmin)
                    <a href="{{ route('medicaments.index') }}">Médicaments</a>
                    <a href="{{ route('lots.index') }}">Entrées de stock</a>
                    <a href="{{ route('ventes.index') }}">Ventes</a>
                    <a href="{{ route('reports.sales') }}">Rapports des ventes</a>
                @endif

                @if ($user->role === 'admin')
                    <a href="{{ route('utilisateurs.index') }}">
                        Gestion des utilisateurs
                    </a>
                @endif

                @if ($isSuperAdmin)
                    <a href="{{ route('admin.licenses.index') }}">
                        Gestion des pharmacies
                    </a>
                @endif

                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar" aria-label="Icône utilisateur">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M5 21v-2a7 7 0 0 1 14 0v2"></path>
                            </svg>
                        </div>

                        <div class="user-details">
                            <span class="user-name">{{ $user->name }}</span>

                            <span class="user-role">
                                @switch($user->role)
                                    @case('super_admin')
                                        Super administrateur
                                        @break
                                    @case('admin')
                                        Administrateur
                                        @break
                                    @case('caissier')
                                        Caissier
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="logout-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="logout-button"
                            title="Déconnexion"
                            aria-label="Déconnexion"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>

                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </nav>
        @endauth
    </header>

    <main>
        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        {{ $appName }} — Gestion de la pharmacie
    </footer>
</body>
</html>
