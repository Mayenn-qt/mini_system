<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    // Owner / Admin View Sales List
    public function index(Request $request)
    {
        $branches = Branch::all();
        $selectedBranch = $request->get('branch_id');
        $search = $request->get('search');
        $date = $request->get('date');

        $query = Sale::with(['branch', 'user', 'items.product']);

        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $sales = $query->latest()->get();

        return view('sales.index', compact('sales', 'branches', 'selectedBranch', 'search', 'date'));
    }

    // Staff Sales History
    public function staffHistory()
    {
        $user = Auth::user();
        $sales = Sale::with(['items.product'])
            ->where('branch_id', $user->branch_id)
            ->latest()
            ->get();

        return view('sales.history', compact('sales'));
    }

    // Staff POS Interface
    public function pos()
    {
        return view('sales.pos');
    }

    // AJAX Product Search for POS
    public function searchProducts(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $query = $request->get('q');

        $products = Product::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['inventories' => function ($invQuery) use ($branchId) {
                $invQuery->where('branch_id', $branchId);
            }])
            ->get()
            ->map(function ($product) {
                $inv = $product->inventories->first();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'selling_price' => $product->selling_price,
                    'stock' => $inv ? $inv->quantity : 0
                ];
            });

        return response()->json($products);
    }

    // Save Walk-in Sale Transaction
    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $user, $branchId) {
            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = intval($itemData['quantity']);

                // Check stock
                $inventory = Inventory::where('branch_id', $branchId)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $quantity) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Decrement stock
                $inventory->decrement('quantity', $quantity);

                // Track movement
                StockMovement::create([
                    'branch_id' => $branchId,
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'sale',
                    'quantity' => $quantity,
                    'notes' => 'Sold via POS',
                ]);

                $subtotal = $product->selling_price * $quantity;
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'buying_price' => $product->buying_price,
                    'selling_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->amount_paid < $totalAmount) {
                throw new \Exception("Amount paid is less than total amount.");
            }

            $changeAmount = $request->amount_paid - $totalAmount;

            $sale = Sale::create([
                'reference_number' => 'SL-' . strtoupper(Str::random(8)),
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'amount_paid' => $request->amount_paid,
                'change_amount' => $changeAmount,
            ]);

            foreach ($itemsToCreate as $item) {
                $item['sale_id'] = $sale->id;
                SaleItem::create($item);
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'Sale recorded successfully!'
            ]);
        });
    }

    // Generate Receipt/Invoice View
    public function receipt(Sale $sale)
    {
        $user = Auth::user();
        
        // Staff can only view their own branch's receipts
        if ($user->isStaff() && $sale->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        $sale->load(['branch', 'user', 'items.product']);
        return view('sales.receipt', compact('sale'));
    }
}
