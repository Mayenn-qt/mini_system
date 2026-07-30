@extends('layouts.app')

@section('title', 'SMS Broadcast Center')
@section('header_title', 'SMS Notification Center')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4 mb-4">
        <!-- New Arrival Broadcast -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="fw-semibold text-accent m-0">Broadcast New Arrival Alert</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sms.arrival') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Product</label>
                            <select name="product_id" id="arrival-product-select" class="form-select" required>
                                <option value="" disabled selected>Choose a newly arrived product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-sku="{{ $product->sku }}" data-name="{{ $product->name }}">
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Simulated Message Template</label>
                            <textarea name="message" id="arrival-message-box" class="form-control" rows="4" required placeholder="Select a product to populate template..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-broadcast me-2"></i> Simulate Broadcast
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts Dispatch -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="fw-semibold text-japanese-red m-0">Low Stock Dispatch Panel</h5>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive border-0 mb-3" style="max-height: 200px; overflow-y: auto;">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-0 bg-transparent text-muted" style="font-size:12px;">Branch</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">SKU</th>
                                        <th class="bg-transparent text-muted" style="font-size:12px;">Stock</th>
                                        <th class="text-end pe-0 bg-transparent text-muted" style="font-size:12px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $inv)
                                        <tr>
                                            <td class="ps-0 fw-semibold text-accent">{{ $inv->branch->name }}</td>
                                            <td class="text-muted">{{ $inv->product->sku }}</td>
                                            <td><span class="badge badge-low-stock">{{ $inv->quantity }} left</span></td>
                                            <td class="text-end pe-0">
                                                <button type="button" class="btn btn-sm btn-outline-danger select-low-stock-btn" 
                                                    data-sku="{{ $inv->product->sku }}" 
                                                    data-branch="{{ $inv->branch->name }}" 
                                                    data-qty="{{ $inv->quantity }}">
                                                    Alert
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Quick Dispatch Form -->
                        <form action="{{ route('sms.lowstock') }}" method="POST" id="low-stock-form" class="d-none">
                            @csrf
                            <input type="hidden" name="product_sku" id="low-sku">
                            <input type="hidden" name="branch" id="low-branch">
                            <input type="hidden" name="quantity" id="low-qty">
                            <div class="mb-3">
                                <label class="form-label">Manager Contact Phone</label>
                                <input type="text" name="phone" class="form-control" value="+63 928 111 2222" required>
                            </div>
                            <div class="p-3 bg-light rounded border mb-3">
                                <span class="fw-semibold text-accent d-block mb-1" style="font-size:13px;">Selected Item Details:</span>
                                <small class="text-muted d-block" id="alert-summary-text"></small>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-send-fill me-2"></i> Send Restock SMS Alert
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4 text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-shield-check text-success fs-2 mb-2"></i>
                            <span>All branch stocks are currently above minimum levels.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dispatch Logs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-transparent pb-0">
            <h5 class="fw-semibold text-accent m-0">SMS Simulation Dispatch Logs</h5>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Alert Category</th>
                            <th>Simulated Details</th>
                            <th>Status</th>
                            <th>Sender ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-muted">{{ $log['timestamp'] }}</td>
                                <td class="fw-semibold text-accent">{{ $log['type'] }}</td>
                                <td style="max-width: 400px; word-wrap: break-word; white-space: normal;">{{ $log['details'] }}</td>
                                <td><span class="badge bg-light text-success border border-success">{{ $log['status'] }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $log['gateway'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No simulated SMS broadcasts sent this session.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Arrival Alert JS template populator
    const productSelect = document.getElementById('arrival-product-select');
    const msgBox = document.getElementById('arrival-message-box');

    if(productSelect) {
        productSelect.addEventListener('change', function() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const name = selectedOption.getAttribute('data-name');
            const sku = selectedOption.getAttribute('data-sku');

            msgBox.value = `Ohaiyo Japan Surplus: Konnichiwa! Our newly arrived [${name}] (SKU: ${sku}) is now in stock at Main Branch. Drop by today to check it out!`;
        });
    }

    // Low stock dispatcher handler
    const lowStockForm = document.getElementById('low-stock-form');
    const alertSummaryText = document.getElementById('alert-summary-text');
    const lowSku = document.getElementById('low-sku');
    const lowBranch = document.getElementById('low-branch');
    const lowQty = document.getElementById('low-qty');

    document.querySelectorAll('.select-low-stock-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sku = btn.getAttribute('data-sku');
            const branch = btn.getAttribute('data-branch');
            const qty = btn.getAttribute('data-qty');

            lowSku.value = sku;
            lowBranch.value = branch;
            lowQty.value = qty;

            alertSummaryText.innerText = `Product SKU: ${sku} | Location: ${branch} | Stock Level: ${qty} units`;
            lowStockForm.classList.remove('d-none');
        });
    });
</script>
@endsection
