@extends('layouts.app')

@section('title', 'Sales Records')
@section('header_title', 'Sales Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('sales.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Search Ref / Staff</label>
                    <input type="text" name="search" class="form-control" placeholder="e.g. SL-ABCDEF" value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if($selectedBranch || $search || $date)
                        <a href="{{ route('sales.index') }}" class="btn btn-light">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Sales List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">Transaction History</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Date / Time</th>
                            <th>Branch</th>
                            <th>Staff Member</th>
                            <th>Total Items</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="fw-semibold text-accent">{{ $sale->reference_number }}</td>
                                <td class="text-muted">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td class="fw-semibold">{{ $sale->branch->name }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>{{ $sale->items->sum('quantity') }} items</td>
                                <td class="text-end fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer me-1"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No sales matching criteria found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
