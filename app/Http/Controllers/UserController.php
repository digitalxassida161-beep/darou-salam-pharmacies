<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('utilisateurs.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('utilisateurs.create');
    }

    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'caissier'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(User $utilisateur)
    {
        return view('utilisateurs.edit', compact('utilisateur'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(Request $request, User $utilisateur)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($utilisateur->id),
            ],
            'role' => ['required', Rule::in(['admin', 'caissier'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Empêcher un administrateur de retirer son propre rôle.
        if (
            auth()->id() === $utilisateur->id
            && $validated['role'] !== 'admin'
        ) {
            return back()
                ->withErrors([
                    'role' => 'Vous ne pouvez pas retirer votre propre rôle administrateur.',
                ])
                ->withInput();
        }

        // Ne pas rétrograder le dernier administrateur.
        if (
            $utilisateur->role === 'admin'
            && $validated['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1
        ) {
            return back()
                ->withErrors([
                    'role' => 'Impossible de rétrograder le dernier administrateur.',
                ])
                ->withInput();
        }

        $utilisateur->name = $validated['name'];
        $utilisateur->email = $validated['email'];
        $utilisateur->role = $validated['role'];

        // Le mot de passe n'est modifié que si un nouveau mot de passe est fourni.
        if (!empty($validated['password'])) {
            $utilisateur->password = $validated['password'];
        }

        $utilisateur->save();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $utilisateur)
    {
        // Empêcher la suppression de son propre compte.
        if (auth()->id() === $utilisateur->id) {
            return back()->with(
                'error',
                'Vous ne pouvez pas supprimer votre propre compte.'
            );
        }

        // Empêcher la suppression du dernier administrateur.
        if (
            $utilisateur->role === 'admin'
            && User::where('role', 'admin')->count() <= 1
        ) {
            return back()->with(
                'error',
                'Impossible de supprimer le dernier administrateur.'
            );
        }

        $utilisateur->delete();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}