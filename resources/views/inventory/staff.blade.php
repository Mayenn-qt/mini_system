@extends('layouts.app')

@section('title', 'Branch Inventory')
@section('header_title', 'Assigned Branch Inventory')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-semibold text-accent">Manage Branch Stock</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockInModal">
                <i class="bi bi-plus-lg me-1"></i> Stock In
            </button>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#stockOutModal">
                <i class="bi bi-dash-lg me-1"></i> Stock Out
            </button>
        </div>
    </div>

    <!-- Inventory Listing -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Stock Levels</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Quantity Available</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inv)
                            <tr>
                                <td class="fw-semibold text-accent">{{ $inv->product->name }}</td>
                                <td class="text-muted">{{ $inv->product->sku }}</td>
                                <td>{{ $inv->product->category->name ?? 'N/A' }}</td>
                                <td class="text-end fw-bold text-accent">{{ $inv->quantity }} units</td>
                                <td class="text-end">
                                    @if($inv->quantity < 5)
                                        <span class="badge badge-low-stock">Low Stock</span>
                                    @else
                                        <span class="badge bg-light text-success border border-success">Well Stocked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No stock loaded for this branch yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Recent Branch Stock Movements</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Product SKU</th>
                            <th>Quantity</th>
                            <th>Type</th>
                            <th>Operator</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mvt)
                            <tr>
                                <td class="text-muted">{{ $mvt->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $mvt->product->sku ?? 'Deleted Product' }}</td>
                                <td class="fw-bold">{{ $mvt->quantity }} units</td>
                                <td>
                                    @if($mvt->type === 'in')
                                        <span class="badge bg-light text-success border border-success">Stock In</span>
                                    @elseif($mvt->type === 'out')
                                        <span class="badge bg-light text-danger border border-danger">Stock Out</span>
                                    @else
                                        <span class="badge bg-light text-primary border border-primary">Sale</span>
                                    @endif
                                </td>
                                <td>{{ $mvt->user->name ?? 'System' }}</td>
                                <td class="text-muted">{{ $mvt->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No movements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff.inventory.in') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Process Stock In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="" disabled selected>Choose product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes / Reference</label>
                        <input type="text" class="form-control" name="notes" placeholder="e.g. Replenishment shipment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success text-white">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff.inventory.out') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Process Stock Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="" disabled selected>Choose product...</option>
                            @foreach($inventories as $inv)
                                <option value="{{ $inv->product->id }}">{{ $inv->product->name }} (Available: {{ $inv->quantity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason / Notes</label>
                        <input type="text" class="form-control" name="notes" placeholder="e.g. Expired, Damaged, Transfer">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Deduct Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
