@extends('layouts.app')

@section('title', 'Global Inventory')
@section('header_title', 'Inventory & Stock Movements')

@section('content')
<div class="container-fluid p-0">
    <!-- Branch Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('inventory.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-accent mb-1" style="font-size:13px;">Filter by Branch</label>
                    <select name="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end h-100">
                    @if($selectedBranch)
                        <a href="{{ route('inventory.index') }}" class="btn btn-light mt-4 w-100">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Listing -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Stock Monitoring</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inv)
                            <tr>
                                <td class="fw-semibold text-accent">{{ $inv->branch->name }}</td>
                                <td>{{ $inv->product->name }}</td>
                                <td class="text-muted">{{ $inv->product->sku }}</td>
                                <td>{{ $inv->product->category->name ?? 'N/A' }}</td>
                                <td class="text-end fw-semibold text-accent">{{ $inv->quantity }} units</td>
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
                                <td colspan="6" class="text-center py-4 text-muted">No inventories matched.</td>
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
            <h5 class="fw-semibold text-accent m-0">Stock Movement History</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Branch</th>
                            <th>Product SKU</th>
                            <th>Quantity</th>
                            <th>Type</th>
                            <th>Authorized By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mvt)
                            <tr>
                                <td class="text-muted">{{ $mvt->created_at->format('M d, Y h:i A') }}</td>
                                <td class="fw-semibold text-accent">{{ $mvt->branch->name }}</td>
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
                                <td class="text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $mvt->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No stock movements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
