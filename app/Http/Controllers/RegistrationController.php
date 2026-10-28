<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pharmacy_name' => ['required', 'string', 'max:150'],
            'pharmacy_address' => ['nullable', 'string', 'max:255'],
            'pharmacy_phone' => ['required', 'string', 'max:30'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user = DB::transaction(function () use ($data) {
            $license = License::create([
                'license_key' => 'DSS-' . strtoupper(Str::random(16)),
                'pharmacy_name' => $data['pharmacy_name'],
                'pharmacy_address' => $data['pharmacy_address'] ?? null,
                'pharmacy_phone' => $data['pharmacy_phone'],
                'installation_date' => today(),
                'maintenance_expires_at' => null,

                // Pas d'abonnement accordé avant un paiement confirmé.
                'subscription_expires_at' => null,
                'status' => 'active',
            ]);

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'admin',
                'license_id' => $license->id,
            ]);
        });

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Inscription enregistrée. Votre compte doit être associé à un abonnement payé avant de pouvoir accéder à la pharmacie.'
            );
    }
}
