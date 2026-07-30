@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('header_title', 'Staff Dashboard')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4 mb-4">
        <!-- Assigned Branch Info -->
        <div class="col-12">
            <div class="card p-4 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-accent mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
                        <p class="text-muted mb-0">Assigned Branch: <strong class="text-japanese-red">{{ $branchName }}</strong></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pos') }}" class="btn btn-primary">
                            <i class="bi bi-calculator me-2"></i> POS Register
                        </a>
                        <a href="{{ route('staff.inventory') }}" class="btn btn-outline-primary">
                            <i class="bi bi-journal-bookmark me-2"></i> Manage Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Today's Branch Sales</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">₱{{ number_format($todaysSales, 2) }}</h3>
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
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Total Stock Items (Branch)</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">{{ $assignedBranchInventory }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-japanese-red">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Low Stock Alerts -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-japanese-red m-0">Low Stock Alerts ({{ $branchName }})</h5>
                </div>
                <div class="card-body">
                    @if($lowStockAlerts->count() > 0)
                        <div class="table-responsive border-0">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Product</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">SKU</th>
                                        <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockAlerts as $inv)
                                        <tr>
                                            <td class="ps-0 fw-semibold text-accent">{{ $inv->product->name }}</td>
                                            <td class="text-muted">{{ $inv->product->sku }}</td>
                                            <td class="text-end pe-0"><span class="badge badge-low-stock">{{ $inv->quantity }} left</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i> All branch products are well stocked.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Recent Branch Transactions</h5>
                </div>
                <div class="card-body">
                    @if($recentSales->count() > 0)
                        <div class="table-responsive border-0">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Ref No.</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">Date</th>
                                        <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales as $sale)
                                        <tr>
                                            <td class="ps-0"><a href="{{ route('sales.receipt', $sale->id) }}" class="text-decoration-none text-japanese-red fw-semibold">{{ $sale->reference_number }}</a></td>
                                            <td class="text-muted">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                            <td class="text-end pe-0 fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            No branch sales recorded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
