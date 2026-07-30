<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isOwner()) {
            return $this->ownerDashboard();
        } else {
            return $this->staffDashboard();
        }
    }

    private function ownerDashboard()
    {
        $totalProducts = Product::count();
        $totalInventory = Inventory::sum('quantity');
        
        // Sales today
        $totalSalesToday = Sale::whereDate('created_at', today())->sum('total_amount');
        
        // Monthly Sales
        $monthlySales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Low stock products (any branch quantity < 5)
        $lowStockProducts = Inventory::with(['product', 'branch'])
            ->where('quantity', '<', 5)
            ->get();

        // Recent Transactions
        $recentTransactions = Sale::with(['branch', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Sales analytics (last 7 days)
        $salesAnalytics = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Branch Performance
        $branches = Branch::withCount('sales')
            ->withSum('sales', 'total_amount')
            ->get();

        // SMS dashboard metrics
        $smsSubscribedCount = \App\Models\Customer::where('subscribed', true)->count();
        $smsSentTodayCount = \App\Models\SmsHistory::whereDate('created_at', today())
            ->where('status', 'Sent')
            ->count();
        $recentSmsActivity = \App\Models\SmsHistory::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.owner', compact(
            'totalProducts',
            'totalInventory',
            'totalSalesToday',
            'monthlySales',
            'lowStockProducts',
            'recentTransactions',
            'salesAnalytics',
            'branches',
            'smsSubscribedCount',
            'smsSentTodayCount',
            'recentSmsActivity'
        ));
    }

    private function staffDashboard()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branchName = $user->branch ? $user->branch->name : 'N/A';

        // Today's Sales in assigned branch
        $todaysSales = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Assigned Branch Inventory
        $assignedBranchInventory = Inventory::where('branch_id', $branchId)
            ->sum('quantity');

        // Low stock alerts in assigned branch
        $lowStockAlerts = Inventory::with('product')
            ->where('branch_id', $branchId)
            ->where('quantity', '<', 5)
            ->get();

        // Recent sales in branch
        $recentSales = Sale::where('branch_id', $branchId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.staff', compact(
            'todaysSales',
            'assignedBranchInventory',
            'lowStockAlerts',
            'recentSales',
            'branchName'
        ));
    }
}
