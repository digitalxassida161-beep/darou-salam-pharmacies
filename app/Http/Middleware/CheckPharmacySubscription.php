<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPharmacySubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Laisser passer les visiteurs non connectés.
        if (!$user) {
            return $next($request);
        }

        // Le Super Admin central n'est pas soumis à un abonnement.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $license = $user->license;

        // Bloquer un compte qui n'est associé à aucune pharmacie.
        if (!$license) {
            abort(403, 'Aucune pharmacie associée à ce compte.');
        }

        // Une pharmacie suspendue ou dont l'abonnement a expiré est bloquée.
        if (
            $license->status !== 'active'
            || !$license->subscription_expires_at
            || $license->subscription_expires_at->isPast()
        ) {
            return response()->view('subscription.expired', [
                'license' => $license,
            ], 403);
        }

        return $next($request);
    }
}
