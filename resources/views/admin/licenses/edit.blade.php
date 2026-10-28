
@extends('layouts.app')

@section('title', 'Modifier une pharmacie')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Modifier une pharmacie</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.licenses.update', $license) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Informations de la pharmacie</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="pharmacy_name" class="form-label">
                        Nom de la pharmacie *
                    </label>
                    <input type="text" id="pharmacy_name"
                           name="pharmacy_name" class="form-control"
                           value="{{ old('pharmacy_name', $license->pharmacy_name) }}"
                           maxlength="150" required>
                </div>

                <div class="mb-3">
                    <label for="pharmacy_address" class="form-label">Adresse</label>
                    <input type="text" id="pharmacy_address"
                           name="pharmacy_address" class="form-control"
                           value="{{ old('pharmacy_address', $license->pharmacy_address) }}"
                           maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="pharmacy_phone" class="form-label">Téléphone</label>
                    <input type="text" id="pharmacy_phone"
                           name="pharmacy_phone" class="form-control"
                           value="{{ old('pharmacy_phone', $license->pharmacy_phone) }}"
                           maxlength="30">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="installation_date" class="form-label">
                            Date d'installation *
                        </label>
                        <input type="date" id="installation_date"
                               name="installation_date" class="form-control"
                               value="{{ old('installation_date', $license->installation_date?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="maintenance_expires_at" class="form-label">
                            Fin de maintenance
                        </label>
                        <input type="date" id="maintenance_expires_at"
                               name="maintenance_expires_at" class="form-control"
                               value="{{ old('maintenance_expires_at', $license->maintenance_expires_at?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Statut *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active"
                            {{ old('status', $license->status) === 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="suspended"
                            {{ old('status', $license->status) === 'suspended' ? 'selected' : '' }}>
                            Suspendue
                        </option>
                        <option value="expired"
                            {{ old('status', $license->status) === 'expired' ? 'selected' : '' }}>
                            Expirée
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Logo de la pharmacie</div>
            <div class="card-body">
                @if ($license->logo)
                    <p>Logo actuel :</p>
                    <img src="{{ asset('storage/' . $license->logo) }}"
                         alt="Logo actuel de {{ $license->pharmacy_name }}"
                         style="max-width:180px; max-height:120px; object-fit:contain;"
                         class="mb-3 d-block">
                @endif

                <label for="logo" class="form-label">Choisir un nouveau logo</label>
                <input type="file" id="logo" name="logo"
                       class="form-control"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                <small class="text-muted">
                    Formats acceptés : JPG, JPEG, PNG ou WEBP. Taille maximale : 2 Mo.
                    Laisse ce champ vide pour conserver le logo actuel.
                </small>

                <div class="mt-3">
                    <img id="logo-preview" alt="Aperçu du nouveau logo"
                         style="display:none; max-width:180px; max-height:120px; object-fit:contain;">
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">
                Enregistrer les modifications
            </button>
            <a href="{{ route('admin.licenses.index') }}"
               class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('logo');
    const preview = document.getElementById('logo-preview');

    input.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            preview.style.display = 'none';
            preview.removeAttribute('src');
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type) || file.size > 2 * 1024 * 1024) {
            alert('Choisis une image JPG, PNG ou WEBP de 2 Mo maximum.');
            this.value = '';
            preview.style.display = 'none';
            preview.removeAttribute('src');
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });
});
</script>
@endsection
