<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'day');

        $start = null;
        $end = null;

        switch ($period) {
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;

            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;

            case 'custom':
                $start = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : Carbon::today();

                $end = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : Carbon::today()->endOfDay();
                break;

            default:
                $period = 'day';
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
        }

        if ($start->gt($end)) {
            return back()->withInput()->withErrors([
                'start_date' => 'La date de début doit être antérieure à la date de fin.',
            ]);
        }

        // Base du rapport : ventes de la période choisie.
        $salesQuery = Sale::query()
            ->whereBetween('created_at', [$start, $end]);

        // Un administrateur voit toutes les ventes.
        // Les autres utilisateurs voient uniquement leurs propres ventes.
        if (!auth()->user()->isAdmin()) {
            $salesQuery->where('user_id', auth()->id());
        }

        // Historique des ventes visibles par cet utilisateur.
        $sales = (clone $salesQuery)
            ->with(['items.medicine', 'user'])
            ->latest()
            ->get();

        // Nombre de ventes.
        $totalSales = (clone $salesQuery)->count();

        // Montant total des ventes.
        $totalAmount = (clone $salesQuery)->sum('total_amount');

        // Répartition des paiements.
        $paymentBreakdown = (clone $salesQuery)
            ->select(
                'payment_method',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_method')
            ->get();

        // Totaux par jour.
        $dailyTotals = (clone $salesQuery)
            ->select(
                DB::raw('DATE(created_at) as sale_date'),
                DB::raw('COUNT(*) as number_of_sales'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date')
            ->get();

        return view('reports.sales', compact(
            'sales',
            'period',
            'start',
            'end',
            'totalSales',
            'totalAmount',
            'paymentBreakdown',
            'dailyTotals'
        ));
    }
}
