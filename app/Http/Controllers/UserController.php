<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Récupérer l'identifiant de la pharmacie de l'administrateur connecté.
     */
    private function licenseId(): int
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'admin' || !$user->license_id) {
            abort(403, 'Accès réservé à l’administrateur de la pharmacie.');
        }

        return (int) $user->license_id;
    }

    /**
     * Vérifier que l'utilisateur appartient à la pharmacie connectée.
     */
    private function verifierPharmacie(User $utilisateur): void
    {
        abort_unless(
            (int) $utilisateur->license_id === $this->licenseId(),
            404
        );
    }

    /**
     * Afficher uniquement les utilisateurs de la pharmacie connectée.
     */
    public function index()
    {
        $licenseId = $this->licenseId();

        $users = User::where('license_id', $licenseId)
            ->whereIn('role', ['admin', 'caissier'])
            ->orderBy('name')
            ->paginate(15);

        return view('utilisateurs.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $this->licenseId();

        return view('utilisateurs.create');
    }

    /**
     * Créer un utilisateur dans la pharmacie connectée.
     */
    public function store(Request $request)
    {
        $licenseId = $this->licenseId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'caissier']),
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'license_id' => $licenseId,
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
        $this->verifierPharmacie($utilisateur);

        abort_if(
            !in_array($utilisateur->role, ['admin', 'caissier'], true),
            404
        );

        return view('utilisateurs.edit', compact('utilisateur'));
    }

    /**
     * Modifier uniquement un utilisateur de la même pharmacie.
     */
    public function update(Request $request, User $utilisateur)
    {
        $licenseId = $this->licenseId();

        $this->verifierPharmacie($utilisateur);

        abort_if(
            !in_array($utilisateur->role, ['admin', 'caissier'], true),
            404
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($utilisateur->id),
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'caissier']),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
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

        // Compter uniquement les administrateurs de cette pharmacie.
        if (
            $utilisateur->role === 'admin'
            && $validated['role'] !== 'admin'
            && User::where('license_id', $licenseId)
                ->where('role', 'admin')
                ->count() <= 1
        ) {
            return back()
                ->withErrors([
                    'role' => 'Impossible de rétrograder le dernier administrateur de cette pharmacie.',
                ])
                ->withInput();
        }

        $utilisateur->name = $validated['name'];
        $utilisateur->email = $validated['email'];
        $utilisateur->role = $validated['role'];

        if (!empty($validated['password'])) {
            $utilisateur->password = $validated['password'];
        }

        $utilisateur->save();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprimer uniquement un utilisateur de la même pharmacie.
     */
    public function destroy(User $utilisateur)
    {
        $licenseId = $this->licenseId();

        $this->verifierPharmacie($utilisateur);

        abort_if(
            !in_array($utilisateur->role, ['admin', 'caissier'], true),
            404
        );

        // Empêcher la suppression de son propre compte.
        if (auth()->id() === $utilisateur->id) {
            return back()->withErrors([
                'utilisateur' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        // Protéger le dernier administrateur de cette pharmacie.
        if (
            $utilisateur->role === 'admin'
            && User::where('license_id', $licenseId)
                ->where('role', 'admin')
                ->count() <= 1
        ) {
            return back()->withErrors([
                'utilisateur' => 'Impossible de supprimer le dernier administrateur de cette pharmacie.',
            ]);
        }

        $utilisateur->delete();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
