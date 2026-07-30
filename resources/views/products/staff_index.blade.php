@extends('layouts.app')

@section('title', 'Branch Products')
@section('header_title', 'Branch Products')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Products List (Assigned Branch Only)</h5>
            <p class="text-muted mb-0 mt-1" style="font-size:13px;">View item information and update stocks for your branch.</p>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Selling Price</th>
                            <th>Current Stock</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $inventory = $product->inventories->first();
                                $stock = $inventory ? $inventory->quantity : 0;
                            @endphp
                            <tr>
                                <td>
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 45px; height: 45px; font-size: 18px; border: 1px solid var(--border-color);">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold text-accent">{{ $product->name }}</td>
                                <td class="text-muted">{{ $product->sku }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="fw-bold text-accent">₱{{ number_format($product->selling_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $stock < 5 ? 'badge-low-stock' : 'bg-light text-dark border' }}">
                                        {{ $stock }} units
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $product->id }}">
                                        <i class="bi bi-arrow-down-up me-1"></i> Update Stock
                                    </button>
                                </td>
                            </tr>

                            <!-- Update Stock Modal -->
                            <div class="modal fade" id="updateStockModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('staff.products.stock', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-semibold">Update Stock: {{ $product->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label d-block">Operation Type</label>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="action" id="actionIn{{ $product->id }}" value="in" checked>
                                                        <label class="btn btn-outline-success" for="actionIn{{ $product->id }}">Stock In (Add)</label>

                                                        <input type="radio" class="btn-check" name="action" id="actionOut{{ $product->id }}" value="out">
                                                        <label class="btn btn-outline-danger" for="actionOut{{ $product->id }}">Stock Out (Remove)</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" min="1" required placeholder="Enter quantity">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notes / Reason</label>
                                                    <input type="text" class="form-control" name="notes" placeholder="e.g. Delivery replenishment, Damaged item">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Process</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
