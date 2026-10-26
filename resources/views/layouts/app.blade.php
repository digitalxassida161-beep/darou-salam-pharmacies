
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Darou Salam Santé') — Pharmacie</title>

    <style>
        :root {
            font-family: Arial, sans-serif;
            color: #172033;
            background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
        }

        /* En-tête */
        header {
            background: #075e54;
            color: #fff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            box-shadow: 0 3px 10px #00000012;
        }

        .brand {
            font-size: 19px;
            font-weight: bold;
        
        }
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
    font-size: 19px;
    font-weight: bold;
}

.brand-text small {
    display: block;
    font-size: 13px;
    font-weight: normal;
    opacity: .85;
}

        .brand a {
            color: #fff;
            text-decoration: none;
        }

        .brand a:hover {
            color: #d1fae5;
        }

        .brand span {
            font-size: 14px;
            font-weight: normal;
            opacity: .85;
        }

        /* Navigation */
        nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 9px 11px;
            border-radius: 7px;
            font-size: 14px;
        }

        nav a:hover {
            background: #087f70;
        }

        nav form {
            margin: 0;
        }

        /* Contenu principal */
        main {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 18px 30px;
        }

        h1, h2, h3 {
            color: #123b35;
        }

        h1 {
            font-size: 26px;
            margin-top: 0;
        }

        h2 {
            font-size: 21px;
        }

        /* Cartes */
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

        /* Formulaires */
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

        /* Boutons */
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

        button:hover, .btn:hover {
            background: #066849;
        }

        .btn-secondary {
            background: #475569;
        }

        .btn-secondary:hover {
            background: #334155;
        }

        .danger {
            color: #b91c1c;
        }

        /* Messages */
        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
            margin-bottom: 16px;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 13px 16px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #fecaca;
        }

        .muted {
            color: #64748b;
        }

        /* Tableaux */
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

        tr:hover td {
            background: #fafdfc;
        }

        .inline {
            display: inline;
        }

        /* Pied de page */
        footer {
            text-align: center;
            padding: 18px;
            color: #64748b;
            font-size: 13px;
        }

        /* Téléphones */
        @media (max-width: 700px) {
            header {
                padding: 15px;
            }

            nav {
                width: 100%;
            }

            nav a {
                padding: 8px;
            }

            main {
                margin-top: 20px;
                padding: 0 12px 24px;
            }

            h1 {
                font-size: 22px;
            }
        }

        /* Impression */
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

            table {
                font-size: 11px;
            }

            th, td {
                padding: 7px;
            }

            h1, h2, h3 {
                color: #000;
            }
        }
    </style>
</head>

<body>

    {{-- En-tête et navigation --}}
    <header>
        
<div class="brand">
    @auth
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Logo Darou Salam Santé"
                class="brand-logo"
            >
            <span class="brand-text">
                Darou Salam Santé
                <small>Pharmacie</small>
            </span>
        </a>
    @else
        <span class="brand-text">
            Darou Salam Santé
            <small>Pharmacie</small>
        </span>
    @endauth
</div>


        @auth
            <nav>
                <a href="{{ route('dashboard') }}">Tableau de bord</a>

                <a href="{{ route('medicaments.index') }}">Médicaments</a>

                <a href="{{ route('lots.index') }}">Entrées de stock</a>

                <a href="{{ route('ventes.index') }}">Ventes</a>

                <a href="{{ route('reports.sales') }}">Rapports des ventes</a>

                <form method="POST"
                      action="{{ route('logout') }}"
                      class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        Déconnexion
                    </button>
                </form>
            </nav>
        @endauth
    </header>

    {{-- Contenu des pages --}}
    <main>
        @if (session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
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
        Darou Salam Santé — Gestion de la pharmacie
    </footer>

</body>
</html>
