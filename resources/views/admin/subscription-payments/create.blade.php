
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enregistrer un paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 650px;">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Renouvellement d'abonnement</h4>
        </div>

        <div class="card-body">
            <p><strong>Pharmacie :</strong> {{ $license->pharmacy_name }}</p>
            <p>
                <strong>Expiration actuelle :</strong>
                {{ $license->subscription_expires_at
                    ? $license->subscription_expires_at->format('d/m/Y')
                    : 'Aucun abonnement enregistré' }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.licenses.payments.store', $license) }}">
                @csrf

                <div class="mb-3">
                    <label for="amount" class="form-label">Montant payé (FCFA)</label>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        class="form-control"
                        min="1"
                        step="1"
                        value="{{ old('amount') }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="payment_date" class="form-label">Date du paiement</label>
                    <input
                        type="date"
                        name="payment_date"
                        id="payment_date"
                        class="form-control"
                        value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Moyen de paiement</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <option value="wave" @selected(old('payment_method') === 'wave')>Wave</option>
                        <option value="orange_money" @selected(old('payment_method') === 'orange_money')>Orange Money</option>
                        <option value="cash" @selected(old('payment_method') === 'cash')>Espèces</option>
                        <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Virement bancaire</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reference" class="form-label">Référence de transaction (facultatif)</label>
                    <input
                        type="text"
                        name="reference"
                        id="reference"
                        class="form-control"
                        value="{{ old('reference') }}">
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (facultatif)</label>
                    <textarea
                        name="notes"
                        id="notes"
                        class="form-control"
                        rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    Enregistrer le paiement
                </button>

                <a href="{{ route('admin.licenses.index') }}" class="btn btn-secondary">
                    Annuler
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
