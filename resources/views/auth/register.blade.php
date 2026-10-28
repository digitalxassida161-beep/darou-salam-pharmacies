
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription pharmacie - DarouSalamPharmacie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f6f4;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 18px #00000012;
        }

        h1 {
            color: #176b45;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 13px;
            border: 0;
            border-radius: 6px;
            background: #176b45;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .errors {
            background: #fff0f0;
            color: #a00000;
            padding: 12px;
            border-radius: 6px;
        }

        .login {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Inscrire ma pharmacie</h1>

    <p>Créez le compte de votre pharmacie sur DarouSalamPharmacie.</p>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <h3>Informations de la pharmacie</h3>

        <label for="pharmacy_name">Nom de la pharmacie *</label>
        <input id="pharmacy_name" name="pharmacy_name"
               value="{{ old('pharmacy_name') }}" required>

        <label for="pharmacy_address">Adresse</label>
        <input id="pharmacy_address" name="pharmacy_address"
               value="{{ old('pharmacy_address') }}">

        <label for="pharmacy_phone">Téléphone *</label>
        <input id="pharmacy_phone" name="pharmacy_phone"
               value="{{ old('pharmacy_phone') }}" required>

        <h3>Compte du responsable</h3>

        <label for="name">Nom complet *</label>
        <input id="name" name="name"
               value="{{ old('name') }}" required>

        <label for="email">Adresse e-mail *</label>
        <input id="email" type="email" name="email"
               value="{{ old('email') }}" required>

        <label for="password">Mot de passe *</label>
        <input id="password" type="password" name="password"
               minlength="8" required>

        <label for="password_confirmation">Confirmer le mot de passe *</label>
        <input id="password_confirmation" type="password"
               name="password_confirmation" minlength="8" required>

        <button type="submit">Créer mon compte</button>
    </form>

    <div class="login">
        Déjà inscrit ?
        <a href="{{ route('login') }}">Se connecter</a>
    </div>
</div>
</body>
</html>
