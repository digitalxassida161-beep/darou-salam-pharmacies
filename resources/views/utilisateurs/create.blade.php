
@extends('layouts.app')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="container">
    <h1>Ajouter un utilisateur</h1>
    <p>Créer un compte administrateur ou caissier.</p>

    @if ($errors->any())
        <div style="background:#f8d7da; color:#842029; padding:12px; margin-bottom:15px; border-radius:6px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('utilisateurs.store') }}" method="POST"
          style="max-width:600px; background:white; padding:20px; border-radius:8px;">
        @csrf

        <div style="margin-bottom:15px;">
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" required
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="role">Rôle</label>
            <select id="role" name="role" required
                    style="display:block; width:100%; padding:10px; margin-top:5px;">
                <option value="">-- Choisir un rôle --</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                    Administrateur
                </option>
                <option value="caissier" {{ old('role') === 'caissier' ? 'selected' : '' }}>
                    Caissier
                </option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                   required minlength="8" autocomplete="new-password"
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <div style="margin-bottom:20px;">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation"
                   name="password_confirmation" required minlength="8"
                   autocomplete="new-password"
                   style="display:block; width:100%; padding:10px; margin-top:5px;">
        </div>

        <button type="submit"
                style="background:#087f5b; color:white; padding:12px 18px; border:0; border-radius:6px; cursor:pointer;">
            Créer l'utilisateur
        </button>

        <a href="{{ route('utilisateurs.index') }}"
           style="margin-left:12px; color:#555;">
            Annuler
        </a>
    </form>
</div>
@endsection
