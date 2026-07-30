@extends('layouts.app')

@section('title', 'Branch Management')
@section('header_title', 'Branch Management')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4 mb-4">
        @foreach($branches as $branch)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold text-accent m-0">{{ $branch->name }}</h5>
                            <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i> {{ $branch->users_count }} Staff</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted" style="font-size:13px;">Total Sales Recorded:</span>
                                <span class="fw-semibold text-accent" style="font-size:13px;">{{ $branch->sales_count }} sales</span>
                            </div>
                        </div>
                        <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i> View Branch Operations
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
