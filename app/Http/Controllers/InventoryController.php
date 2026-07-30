<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    // Owner / Admin View Inventory
    public function index(Request $request)
    {
        $branches = Branch::all();
        $selectedBranch = $request->get('branch_id');

        $query = Inventory::with(['product.category', 'branch']);

        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        }

        $inventories = $query->get();
        $movements = StockMovement::with(['product', 'branch', 'user'])->latest()->take(50)->get();

        return view('inventory.index', compact('inventories', 'branches', 'selectedBranch', 'movements'));
    }

    // Staff View Inventory & Stock Movements
    public function staffIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $inventories = Inventory::with(['product.category'])
            ->where('branch_id', $branchId)
            ->get();

        $movements = StockMovement::with(['product', 'user'])
            ->where('branch_id', $branchId)
            ->latest()
            ->take(50)
            ->get();

        $products = Product::all();

        return view('inventory.staff', compact('inventories', 'movements', 'products'));
    }

    // Staff Stock In
    public function stockIn(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $inventory = Inventory::where('branch_id', $branchId)
            ->where('product_id', $request->product_id)
            ->firstOrCreate([
                'branch_id' => $branchId,
                'product_id' => $request->product_id,
            ], ['quantity' => 0]);

        $inventory->increment('quantity', $request->quantity);

        StockMovement::create([
            'branch_id' => $branchId,
            'product_id' => $request->product_id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'Manual Stock In',
        ]);

        return redirect()->route('staff.inventory')->with('success', 'Stock added successfully.');
    }

    // Staff Stock Out
    public function stockOut(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $inventory = Inventory::where('branch_id', $branchId)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$inventory || $inventory->quantity < $request->quantity) {
            return redirect()->route('staff.inventory')->with('error', 'Insufficient stock.');
        }

        $inventory->decrement('quantity', $request->quantity);

        StockMovement::create([
            'branch_id' => $branchId,
            'product_id' => $request->product_id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'Manual Stock Out',
        ]);

        return redirect()->route('staff.inventory')->with('success', 'Stock removed successfully.');
    }
}
