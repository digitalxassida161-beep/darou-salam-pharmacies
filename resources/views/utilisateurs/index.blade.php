@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <div>
            <h1>Gestion des utilisateurs</h1>
            <p>Gérez les comptes des administrateurs et des caissiers.</p>
        </div>

        <a href="{{ route('utilisateurs.create') }}"
           style="background:#087f5b; color:white; padding:12px 18px; border-radius:6px; text-decoration:none;">
            + Ajouter un utilisateur
        </a>
    </div>

    @if(session('success'))
        <div style="background:#d1e7dd; color:#0f5132; padding:12px; border-radius:6px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#f8d7da; color:#842029; padding:12px; border-radius:6px; margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="overflow-x:auto; background:white; border-radius:8px; padding:15px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f1f5f9; text-align:left;">
                    <th style="padding:12px;">Nom</th>
                    <th style="padding:12px;">Adresse e-mail</th>
                    <th style="padding:12px;">Rôle</th>
                    <th style="padding:12px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:12px;">{{ $user->name }}</td>
                        <td style="padding:12px;">{{ $user->email }}</td>
                        <td style="padding:12px;">
                            @if($user->role === 'admin')
                                <span style="background:#dbeafe; color:#1d4ed8; padding:5px 9px; border-radius:12px;">
                                    Administrateur
                                </span>
                            @else
                                <span style="background:#dcfce7; color:#166534; padding:5px 9px; border-radius:12px;">
                                    Caissier
                                </span>
                            @endif
                        </td>

                        <td style="padding:12px; white-space:nowrap;">
                            <a href="{{ route('utilisateurs.edit', $user) }}"
                               style="color:#087f5b; margin-right:10px;">
                                Modifier
                            </a>

                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('utilisateurs.destroy', $user) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            style="background:#dc2626; color:white; border:none; padding:7px 10px; border-radius:5px; cursor:pointer;">
                                        Supprimer
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:20px; text-align:center;">
                            Aucun utilisateur enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection