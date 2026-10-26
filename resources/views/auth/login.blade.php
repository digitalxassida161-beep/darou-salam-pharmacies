@extends('layouts.app')
@section('title', 'Connexion')
@section('content')
<div class="card" style="max-width:460px;margin:45px auto">
    <h1>Connexion</h1>
    <p class="muted">Poste de Santé de Darou Salam</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label>Adresse e-mail</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <label style="font-weight:normal"><input style="width:auto" type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
        <button type="submit">Se connecter</button>
    </form>
    <p class="muted">Utilisez votre compte personnel. Ne partagez pas votre mot de passe.</p>
</div>
@endsection
