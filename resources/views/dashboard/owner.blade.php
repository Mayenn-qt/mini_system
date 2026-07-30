@extends('layouts.app')

@section('title', 'Owner Dashboard')
@section('header_title', 'Dashboard Overview')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Total Products</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">{{ $totalProducts }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-japanese-red">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Total Stock</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">{{ $totalInventory }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-japanese-red">
                        <i class="bi bi-journal-bookmark fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Sales Today</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">₱{{ number_format($totalSalesToday, 2) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-success">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Monthly Sales</span>
                        <h3 class="fw-bold mt-1 text-accent mb-0">₱{{ number_format($monthlySales, 2) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-primary">
                        <i class="bi bi-calendar3 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Chart & Branch Performance -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Sales Performance (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Branch Performance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Branch Performance</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach($branches as $branch)
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-accent" style="font-size:14px;">{{ $branch->name }}</span>
                                    <span class="fw-bold text-muted" style="font-size:13px;">₱{{ number_format($branch->sales_sum_total_amount ?? 0, 2) }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    @php
                                        $percent = ($monthlySales > 0) ? (($branch->sales_sum_total_amount ?? 0) / $monthlySales) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
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
                    <h5 class="card-title fw-semibold text-japanese-red m-0">Low Stock Alerts</h5>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive border-0">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Product</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">Branch</th>
                                        <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $inv)
                                        <tr>
                                            <td class="ps-0 fw-semibold text-accent">{{ $inv->product->name }} <br><small class="text-muted">{{ $inv->product->sku }}</small></td>
                                            <td class="text-muted">{{ $inv->branch->name }}</td>
                                            <td class="text-end pe-0"><span class="badge badge-low-stock">{{ $inv->quantity }} left</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i> All products are well stocked.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title fw-semibold text-accent m-0">Recent Transactions</h5>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive border-0">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Ref No.</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">Branch</th>
                                        <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $sale)
                                        <tr>
                                            <td class="ps-0"><a href="{{ route('sales.receipt', $sale->id) }}" class="text-decoration-none text-japanese-red fw-semibold">{{ $sale->reference_number }}</a></td>
                                            <td class="text-muted">{{ $sale->branch->name }}</td>
                                            <td class="text-end pe-0 fw-bold text-accent">₱{{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            No transactions recorded today.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Parse PHP data into arrays
    const salesData = @json($salesAnalytics);
    const labels = salesData.map(item => item.date);
    const data = salesData.map(item => item.total);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length > 0 ? labels : ['No Data'],
            datasets: [{
                label: 'Daily Revenue (₱)',
                data: data.length > 0 ? data : [0],
                borderColor: '#C8102E',
                backgroundColor: 'rgba(200, 16, 46, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection
