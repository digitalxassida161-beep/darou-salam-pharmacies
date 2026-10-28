<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineLot;
use App\Models\Sale;
use App\Models\License;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tableau de bord central du Super Admin
        if ($user->role === 'super_admin') {
            $pharmacyCount = License::count();

            $activePharmacies = License::where('status', 'active')
                ->count();

            $usersCount = \App\Models\User::count();

            return view('dashboard', compact(
                'pharmacyCount',
                'activePharmacies',
                'usersCount'
            ) + [
                'isSuperAdmin' => true,
            ]);
        }

        // Tableau de bord de chaque pharmacie
        $licenseId = $user->license_id;

        abort_if(
            !$licenseId,
            403,
            'Aucune pharmacie associée à ce compte.'
        );

        $todaySales = Sale::where('license_id', $licenseId)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $salesCount = Sale::where('license_id', $licenseId)
            ->whereDate('created_at', today())
            ->count();

        $medicineCount = Medicine::where('license_id', $licenseId)
            ->where('active', true)
            ->count();

        $lowStock = Medicine::where('license_id', $licenseId)
            ->withSum([
                'lots as stock_total' => function ($q) use ($licenseId) {
                    $q->where('license_id', $licenseId)
                        ->whereDate('expiry_date', '>=', today());
                }
            ], 'quantity')
            ->get()
            ->filter(fn ($m) =>
                (int) $m->stock_total <= $m->minimum_stock
            );

        $expiringLots = MedicineLot::where('license_id', $licenseId)
            ->whereHas('medicine', function ($q) use ($licenseId) {
                $q->where('license_id', $licenseId);
            })
            ->with('medicine')
            ->where('quantity', '>', 0)
            ->whereBetween('expiry_date', [
                today(),
                today()->copy()->addDays(90)
            ])
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'salesCount',
            'medicineCount',
            'lowStock',
            'expiringLots'
        ) + [
            'isSuperAdmin' => false,
        ]);
    }
}
