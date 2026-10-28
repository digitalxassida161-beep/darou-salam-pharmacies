<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubscriptionPaymentController extends Controller
{
    public function create(License $license)
    {
        return view(
            'admin.subscription-payments.create',
            compact('license')
        );
    }

    public function store(Request $request, License $license)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => [
                'required',
                Rule::in([
                    'wave',
                    'orange_money',
                    'cash',
                    'bank_transfer',
                ]),
            ],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $license, $data) {

            // Recharger et verrouiller la licence pendant la transaction.
            $license = License::whereKey($license->id)
                ->lockForUpdate()
                ->firstOrFail();

            $today = Carbon::today();
            $currentExpiry = $license->subscription_expires_at;

            // Si l'abonnement est toujours valide, prolonger à sa suite.
            if (
                $currentExpiry &&
                $currentExpiry->greaterThanOrEqualTo($today)
            ) {
                $periodStart = $currentExpiry->copy()->addDay();
            } else {
                // Si l'abonnement est expiré ou absent, démarrer aujourd'hui.
                $periodStart = $today->copy();
            }

            // Calculer un mois de service, sans débordement de calendrier.
            $periodEnd = $periodStart->copy()
                ->addMonthNoOverflow()
                ->subDay();

            // Enregistrer le paiement.
            SubscriptionPayment::create([
                'license_id' => $license->id,
                'amount' => $data['amount'],
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);

            // Mettre à jour la fin de l'abonnement.
            $license->subscription_expires_at = $periodEnd;

            // Réactiver une licence expirée, mais pas une licence suspendue.
            if ($license->status === 'expired') {
                $license->status = 'active';
            }

            $license->save();
        });

        return redirect()
            ->route('admin.licenses.index')
            ->with(
                'success',
                'Paiement enregistré et abonnement prolongé avec succès.'
            );
    }
}
