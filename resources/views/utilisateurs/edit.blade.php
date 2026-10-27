```blade
@extends('layouts.app')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="container">
    <h1>Modifier un utilisateur</h1>
    <p>Modifiez les informations du compte et son rôle.</p>

    @if ($errors->any())
        <div style="background:#f8d7da; color:#842029; padding:12px; border-radius:6px; margin-bottom:15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('utilisateurs.update', $utilisateur) }}"
          method="POST"
          style="max-width:600px; background:white; padding:20px; border-radius:8px;">

        @csrf
        @method('PUT')

        <div style="margin-bottom:15px;">
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $utilisateur->name) }}"
                   required
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $utilisateur->email) }}"
                   required
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="role">Rôle</label>
            <select id="role" name="role" required
                    style="display:block; width:100%; padding:10px; margin-top:5px;">
                <option value="admin"
                    {{ old('role', $utilisateur->role) === 'admin' ? 'selected' : '' }}>
                    Administrateur
                </option>

                <option value="caissier"
                    {{ old('role', $utilisateur->role) === 'caissier' ? 'selected' : '' }}>
                    Caissier
                </option>
            </select>
        </div>

        <hr style="margin:20px 0;">

        <p>Laissez les champs du mot de passe vides si vous ne souhaitez pas le modifier.</p>

        <div style="margin-bottom:15px;">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" id="password" name="password"
                   minlength="8" autocomplete="new-password"
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:20px;">
            <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <input type="password" id="password_confirmation"
                   name="password_confirmation" minlength="8"
                   autocomplete="new-password"
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <button type="submit"
                style="background:#087f5b; color:white; padding:12px 18px; border:0; border-radius:6px; cursor:pointer;">
            Enregistrer les modifications
        </button>

        <a href="{{ route('utilisateurs.index') }}"
           style="margin-left:12px; color:#555;">
            Annuler
        </a>
    </form>
</div>
@endsection
