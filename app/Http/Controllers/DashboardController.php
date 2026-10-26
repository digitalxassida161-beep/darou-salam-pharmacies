<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineLot;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $salesCount = Sale::whereDate('created_at', today())->count();
        $medicineCount = Medicine::where('active', true)->count();
        $lowStock = Medicine::withSum(['lots as stock_total' => function ($q) {
            $q->whereDate('expiry_date', '>=', today());
        }], 'quantity')->get()->filter(fn ($m) => (int) $m->stock_total <= $m->minimum_stock);
        $expiringLots = MedicineLot::with('medicine')
            ->where('quantity', '>', 0)
            ->whereBetween('expiry_date', [today(), today()->copy()->addDays(90)])
            ->orderBy('expiry_date')->limit(10)->get();

        return view('dashboard', compact('todaySales', 'salesCount', 'medicineCount', 'lowStock', 'expiringLots'));
    }
}
