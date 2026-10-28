<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Identifiants incorrects.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Le Super Admin peut toujours se connecter.
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('dashboard'));
        }

        // Vérifier que le compte est associé à une pharmacie.
        $license = $user->license;

        if (!$license) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Aucune pharmacie n’est associée à ce compte. Contactez le Super Admin.',
                ]);
        }

        // Une licence suspendue ne permet pas de se connecter.
        if ($license->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Cette licence est suspendue ou inactive. Contactez le Super Admin.',
                ]);
        }

        // Refuser les pharmacies sans abonnement ou dont l'abonnement a expiré.
        if (
            !$license->subscription_expires_at ||
            $license->subscription_expires_at->lt(today())
        ) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Votre abonnement est absent ou expiré. Veuillez contacter le Super Admin pour renouveler votre abonnement.',
                ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
