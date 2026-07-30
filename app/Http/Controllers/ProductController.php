<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'inventories.branch'])->get();
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0', // Initial quantity for Main Branch
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'image_path' => $imagePath,
        ]);

        // Seed initial quantities to branches. Set the input quantity in Main Branch, 0 in others
        $branches = Branch::all();
        foreach ($branches as $branch) {
            $qty = ($branch->name === 'Main Branch') ? $request->quantity : 0;
            
            Inventory::create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'quantity' => $qty,
            ]);

            if ($qty > 0) {
                StockMovement::create([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $qty,
                    'notes' => 'Initial stock creation',
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    // View products list for Staff (Read-only plus stock edit shortcut if assigned)
    public function staffIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        
        $products = Product::with(['category', 'inventories' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->get();

        return view('products.staff_index', compact('products'));
    }

    // Staff quick stock updates
    public function updateStock(Request $request, Product $product)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $request->validate([
            'quantity' => 'required|integer|min:0',
            'action' => 'required|in:in,out',
            'notes' => 'nullable|string|max:255',
        ]);

        $inventory = Inventory::where('branch_id', $branchId)
            ->where('product_id', $product->id)
            ->firstOrCreate([
                'branch_id' => $branchId,
                'product_id' => $product->id,
            ], ['quantity' => 0]);

        $prevQty = $inventory->quantity;

        if ($request->action === 'in') {
            $newQty = $prevQty + $request->quantity;
        } else {
            if ($prevQty < $request->quantity) {
                return back()->with('error', 'Insufficient stock for this operation.');
            }
            $newQty = $prevQty - $request->quantity;
        }

        $inventory->update(['quantity' => $newQty]);

        StockMovement::create([
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => $request->action,
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'Staff manual stock update',
        ]);

        return back()->with('success', 'Stock updated successfully!');
    }
}
