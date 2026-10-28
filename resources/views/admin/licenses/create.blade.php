
@extends('layouts.app')

@section('title', 'Enregistrer une pharmacie')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h1>Enregistrer une pharmacie</h1>
        <p class="text-muted">
            Ajouter une pharmacie cliente à Darou Salam Santé
            et créer son compte administrateur.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.licenses.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        {{-- INFORMATIONS DE LA PHARMACIE --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">1. Informations de la pharmacie</h5>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label for="pharmacy_name" class="form-label">
                        Nom de la pharmacie *
                    </label>
                    <input
                        type="text"
                        id="pharmacy_name"
                        name="pharmacy_name"
                        class="form-control"
                        value="{{ old('pharmacy_name') }}"
                        maxlength="150"
                        required
                    >
                    @error('pharmacy_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pharmacy_address" class="form-label">
                        Adresse
                    </label>
                    <input
                        type="text"
                        id="pharmacy_address"
                        name="pharmacy_address"
                        class="form-control"
                        value="{{ old('pharmacy_address') }}"
                        maxlength="255"
                    >
                    @error('pharmacy_address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pharmacy_phone" class="form-label">
                        Téléphone
                    </label>
                    <input
                        type="text"
                        id="pharmacy_phone"
                        name="pharmacy_phone"
                        class="form-control"
                        value="{{ old('pharmacy_phone') }}"
                        maxlength="30"
                    >
                    @error('pharmacy_phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- LOGO DE LA PHARMACIE --}}
                <div class="mb-3">
                    <label for="logo" class="form-label">
                        Logo de la pharmacie
                    </label>

                    <input
                        type="file"
                        id="logo"
                        name="logo"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >

                    <small class="text-muted">
                        Formats acceptés : JPG, JPEG, PNG et WEBP.
                        Taille maximale : 2 Mo. Ce champ est facultatif.
                    </small>

                    @error('logo')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <div class="mt-3">
                        <img
                            id="logo-preview"
                            src="#"
                            alt="Aperçu du logo"
                            style="display:none; max-width:150px; max-height:150px; object-fit:contain; border:1px solid #ddd; padding:8px; border-radius:8px;"
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="installation_date" class="form-label">
                            Date d'installation *
                        </label>
                        <input
                            type="date"
                            id="installation_date"
                            name="installation_date"
                            class="form-control"
                            value="{{ old('installation_date', date('Y-m-d')) }}"
                            required
                        >
                        @error('installation_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="maintenance_expires_at" class="form-label">
                            Fin de maintenance
                        </label>
                        <input
                            type="date"
                            id="maintenance_expires_at"
                            name="maintenance_expires_at"
                            class="form-control"
                            value="{{ old('maintenance_expires_at') }}"
                        >
                        @error('maintenance_expires_at')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- COMPTE ADMINISTRATEUR --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">2. Compte administrateur</h5>
            </div>

            <div class="card-body">

                <div class="alert alert-info">
                    Ce compte permettra à l'administrateur de se connecter
                    et de gérer uniquement cette pharmacie.
                </div>

                <div class="mb-3">
                    <label for="admin_name" class="form-label">
                        Nom complet de l'administrateur *
                    </label>
                    <input
                        type="text"
                        id="admin_name"
                        name="admin_name"
                        class="form-control"
                        value="{{ old('admin_name') }}"
                        maxlength="255"
                        required
                    >
                    @error('admin_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="admin_email" class="form-label">
                        Adresse e-mail de connexion *
                    </label>
                    <input
                        type="email"
                        id="admin_email"
                        name="admin_email"
                        class="form-control"
                        value="{{ old('admin_email') }}"
                        maxlength="255"
                        autocomplete="username"
                        required
                    >
                    @error('admin_email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="admin_password" class="form-label">
                            Mot de passe *
                        </label>
                        <input
                            type="password"
                            id="admin_password"
                            name="admin_password"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                        @error('admin_password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            8 caractères minimum.
                        </small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="admin_password_confirmation" class="form-label">
                            Confirmer le mot de passe *
                        </label>
                        <input
                            type="password"
                            id="admin_password_confirmation"
                            name="admin_password_confirmation"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>
                </div>

            </div>
        </div>

        {{-- BOUTONS --}}
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">
                Enregistrer la pharmacie et son administrateur
            </button>

            <a
                href="{{ route('admin.licenses.index') }}"
                class="btn btn-secondary"
            >
                Annuler
            </a>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('logo');
    const preview = document.getElementById('logo-preview');

    input.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            preview.removeAttribute('src');
            preview.style.display = 'none';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            alert('Choisissez une image JPG, PNG ou WEBP.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Le logo ne doit pas dépasser 2 Mo.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'inline-block';
    });
});
</script>
@endsection
