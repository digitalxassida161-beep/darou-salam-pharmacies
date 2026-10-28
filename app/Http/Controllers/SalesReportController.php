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
        $user = auth()->user();
        $licenseId = $user->license_id;

        abort_if(
            !$licenseId,
            403,
            'Aucune pharmacie associée à ce compte.'
        );

        $period = $request->input('period', 'day');

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
                $request->validate([
                    'start_date' => ['nullable', 'date'],
                    'end_date' => [
                        'nullable',
                        'date',
                        'after_or_equal:start_date',
                    ],
                ]);

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
                'start_date' =>
                    'La date de début doit être antérieure à la date de fin.',
            ]);
        }

        // Base commune : ventes de la pharmacie connectée.
        $salesQuery = Sale::where('license_id', $licenseId)
            ->whereBetween('created_at', [$start, $end]);

        // Le caissier ne voit que les ventes qu'il a enregistrées.
        if ($user->role === 'caissier') {
            $salesQuery->where('user_id', $user->id);
        }

        // Les statistiques utilisent exactement le même périmètre.
        $sales = (clone $salesQuery)
            ->with(['items.medicine', 'user'])
            ->latest()
            ->get();

        $totalSales = (clone $salesQuery)->count();

        $totalAmount = (clone $salesQuery)->sum('total_amount');

        $paymentBreakdown = (clone $salesQuery)
            ->select(
                'payment_method',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_method')
            ->get();

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
