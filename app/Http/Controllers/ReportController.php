<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branches = Branch::all();
        
        $filterBranch = $request->get('branch_id');
        $filterType = $request->get('type', 'daily'); // daily, weekly, monthly, branch, inventory
        
        $query = Sale::with(['branch', 'user', 'items.product']);

        if ($user->isStaff()) {
            $filterBranch = $user->branch_id;
        }

        if ($filterBranch) {
            $query->where('branch_id', $filterBranch);
        }

        // Apply Time filters
        if ($filterType === 'daily') {
            $query->whereDate('created_at', today());
        } elseif ($filterType === 'weekly') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($filterType === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $sales = $query->latest()->get();
        $totalSales = $sales->sum('total_amount');
        
        // Calculate Profit (selling_price - buying_price) * quantity
        $totalProfit = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalProfit += ($item->selling_price - $item->buying_price) * $item->quantity;
            }
        }

        // Branch breakdown for reports
        $branchReport = [];
        if ($user->isOwner() && $filterType === 'branch') {
            $branchReport = Branch::withSum('sales', 'total_amount')->get();
        }

        // Inventory Reports
        $inventoryReport = [];
        if ($filterType === 'inventory') {
            $invQuery = Inventory::with(['product.category', 'branch']);
            if ($user->isStaff()) {
                $invQuery->where('branch_id', $user->branch_id);
            } elseif ($filterBranch) {
                $invQuery->where('branch_id', $filterBranch);
            }
            $inventoryReport = $invQuery->get();
        }

        return view('reports.index', compact(
            'sales',
            'totalSales',
            'totalProfit',
            'branches',
            'filterBranch',
            'filterType',
            'branchReport',
            'inventoryReport'
        ));
    }

    // Mock HTML/PDF Printable Export View
    public function export(Request $request)
    {
        $user = Auth::user();
        $filterBranch = $request->get('branch_id');
        $filterType = $request->get('type', 'daily');

        if ($user->isStaff()) {
            $filterBranch = $user->branch_id;
        }

        $branchName = 'All Branches';
        if ($filterBranch) {
            $branch = Branch::find($filterBranch);
            if ($branch) $branchName = $branch->name;
        }

        $query = Sale::with(['branch', 'user', 'items.product']);
        if ($filterBranch) {
            $query->where('branch_id', $filterBranch);
        }

        if ($filterType === 'daily') {
            $query->whereDate('created_at', today());
            $title = "Daily Sales Report - " . today()->format('Y-m-d');
        } elseif ($filterType === 'weekly') {
            $query->where('created_at', '>=', now()->subWeek());
            $title = "Weekly Sales Report (Last 7 Days)";
        } elseif ($filterType === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            $title = "Monthly Sales Report - " . now()->format('F Y');
        } else {
            $title = "Sales Report";
        }

        $sales = $query->latest()->get();
        $totalSales = $sales->sum('total_amount');

        $totalProfit = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalProfit += ($item->selling_price - $item->buying_price) * $item->quantity;
            }
        }

        return view('reports.export', compact('sales', 'totalSales', 'totalProfit', 'title', 'branchName', 'filterType'));
    }
}
