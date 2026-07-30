@extends('layouts.app')

@section('title', 'Sales History')
@section('header_title', 'My Branch Sales History')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Recent Branch Transactions</h5>
            <p class="text-muted mb-0 mt-1" style="font-size:13px;">View past orders checked out at your branch location.</p>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Date / Time</th>
                            <th>Items Sold</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Total Bill</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="fw-semibold text-accent">{{ $sale->reference_number }}</td>
                                <td class="text-muted">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $sale->items->sum('quantity') }} units</td>
                                <td class="text-end text-muted">₱{{ number_format($sale->amount_paid, 2) }}</td>
                                <td class="text-end fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer me-1"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No sales found for this branch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
