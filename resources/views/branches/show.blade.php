@extends('layouts.app')

@section('title', $branch->name . ' Details')
@section('header_title', $branch->name . ' Analytics')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-semibold text-accent m-0">{{ $branch->name }}</h5>
            <p class="text-muted m-0" style="font-size:13px;">Branch details, inventory levels, and transaction logs.</p>
        </div>
        <a href="{{ route('branches.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Branches
        </a>
    </div>

    <!-- Branch Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Aggregate Sales Revenue</span>
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
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Active Staff Members</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">{{ $branch->users->count() }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-japanese-red">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Branch Inventory -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Inventory Levels</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive border-0">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Product</th>
                                    <th class="bg-transparent text-muted" style="font-size:12px;">SKU</th>
                                    <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventories as $inv)
                                    <tr>
                                        <td class="ps-0 fw-semibold text-accent">{{ $inv->product->name }}</td>
                                        <td class="text-muted">{{ $inv->product->sku }}</td>
                                        <td class="text-end pe-0 fw-bold"><span class="badge {{ $inv->quantity < 5 ? 'badge-low-stock' : 'bg-light text-dark border' }}">{{ $inv->quantity }} units</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No stock catalogued.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Transaction Log</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive border-0">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Ref No.</th>
                                    <th class="bg-transparent text-muted" style="font-size:12px;">Cashier</th>
                                    <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="ps-0 fw-semibold text-japanese-red"><a href="{{ route('sales.receipt', $sale->id) }}" class="text-decoration-none text-japanese-red" target="_blank">{{ $sale->reference_number }}</a></td>
                                        <td>{{ $sale->user->name }}</td>
                                        <td class="text-end pe-0 fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No sales recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
