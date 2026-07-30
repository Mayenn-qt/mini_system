@extends('layouts.app')

@section('title', 'Reports Center')
@section('header_title', 'Analytics & Reports')

@section('content')
<div class="container-fluid p-0">
    <!-- Configuration Panel -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:13px;">Report Category</label>
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="daily" {{ $filterType === 'daily' ? 'selected' : '' }}>Daily Sales Report</option>
                        <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Weekly Sales Report</option>
                        <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Monthly Sales Report</option>
                        @if(auth()->user()->isOwner())
                            <option value="branch" {{ $filterType === 'branch' ? 'selected' : '' }}>Branch Performance Report</option>
                        @endif
                        <option value="inventory" {{ $filterType === 'inventory' ? 'selected' : '' }}>Stock Level Status Report</option>
                    </select>
                </div>

                @if(auth()->user()->isOwner() && $filterType !== 'branch')
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Filter by Location</label>
                        <select name="branch_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $filterBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-4 d-flex align-items-end gap-2 justify-content-end">
                    @if($filterType !== 'branch' && $filterType !== 'inventory')
                        <a href="{{ route('reports.export', ['type' => $filterType, 'branch_id' => $filterBranch]) }}" target="_blank" class="btn btn-primary w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Export Report
                        </a>
                    </div>
                    @endif
            </form>
        </div>
    </div>

    <!-- SALES REPORT TYPE -->
    @if(in_array($filterType, ['daily', 'weekly', 'monthly']))
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card p-3 border-0 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Gross Revenue</span>
                            <h3 class="fw-bold mt-1 text-accent mb-0">₱{{ number_format($totalSales, 2) }}</h3>
                        </div>
                        <div class="rounded-circle p-3 bg-light text-success">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 border-0 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Estimated Profit</span>
                            <h3 class="fw-bold mt-1 text-japanese-red mb-0">₱{{ number_format($totalProfit, 2) }}</h3>
                        </div>
                        <div class="rounded-circle p-3 bg-light text-primary">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent pb-0">
                <h5 class="fw-semibold text-accent m-0">Detailed Sales Listings</h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Ref Number</th>
                                <th>Branch</th>
                                <th>Billing Date</th>
                                <th>Cashier</th>
                                <th>Items</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="fw-semibold text-accent">{{ $sale->reference_number }}</td>
                                    <td>{{ $sale->branch->name }}</td>
                                    <td class="text-muted">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td>{{ $sale->items->sum('quantity') }} units</td>
                                    <td class="text-end fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No sales matched the specified filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- BRANCH COMPARISON TYPE -->
    @if($filterType === 'branch' && auth()->user()->isOwner())
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent pb-0">
                <h5 class="fw-semibold text-accent m-0">Branch Sales Breakdown</h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Transactions Count</th>
                                <th class="text-end">Total Gross Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchReport as $br)
                                <tr>
                                    <td class="fw-semibold text-accent">{{ $br->name }}</td>
                                    <td>{{ $br->sales_sum_total_amount ? \App\Models\Sale::where('branch_id', $br->id)->count() : 0 }} records</td>
                                    <td class="text-end fw-bold text-accent">₱{{ number_format($br->sales_sum_total_amount ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No branch reports generated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- INVENTORY REPORT TYPE -->
    @if($filterType === 'inventory')
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent pb-0">
                <h5 class="fw-semibold text-accent m-0">Current Branch Stock Sheets</h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Product Item</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th class="text-end">Available Stock</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryReport as $inv)
                                <tr>
                                    <td class="fw-semibold text-accent">{{ $inv->branch->name }}</td>
                                    <td>{{ $inv->product->name }}</td>
                                    <td class="text-muted">{{ $inv->product->sku }}</td>
                                    <td>{{ $inv->product->category->name ?? 'N/A' }}</td>
                                    <td class="text-end fw-bold text-accent">{{ $inv->quantity }} units</td>
                                    <td class="text-end">
                                        @if($inv->quantity < 5)
                                            <span class="badge badge-low-stock">Restock Needed</span>
                                        @else
                                            <span class="badge bg-light text-success border border-success">Good</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No stock data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
