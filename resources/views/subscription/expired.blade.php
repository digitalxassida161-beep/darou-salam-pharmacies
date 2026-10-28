
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abonnement expiré - Darou Salam Pharmacie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm border-0 text-center p-4" style="max-width: 520px;">
            <div class="card-body">
                <h1 class="text-danger mb-3">Abonnement expiré</h1>

                <h5 class="mb-3">{{ $license->pharmacy_name }}</h5>

                <p class="text-muted">
                    L'accès à la gestion de cette pharmacie est suspendu,
                    car son abonnement a expiré ou sa licence est suspendue.
                </p>

                <p>
                    <strong>Date d'expiration :</strong>
                    {{ $license->subscription_expires_at
                        ? $license->subscription_expires_at->format('d/m/Y')
                        : 'Non définie' }}
                </p>

                <p>Veuillez contacter le Super Administrateur pour renouveler votre abonnement.</p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
