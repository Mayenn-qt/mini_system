<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['users', 'sales'])->get();
        return view('branches.index', compact('branches'));
    }

    public function show(Branch $branch)
    {
        $branch->load(['users']);
        
        $inventories = Inventory::with(['product.category'])
            ->where('branch_id', $branch->id)
            ->get();

        $sales = Sale::with(['user'])
            ->where('branch_id', $branch->id)
            ->latest()
            ->take(50)
            ->get();

        $totalSales = $sales->sum('total_amount');

        return view('branches.show', compact('branch', 'inventories', 'sales', 'totalSales'));
    }
}
