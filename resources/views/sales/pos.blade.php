@extends('layouts.app')

@section('title', 'POS Sales Recording')
@section('header_title', 'Point of Sale (POS) Register')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Product Search & Selector -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-transparent pb-0">
                    <h5 class="fw-semibold text-accent m-0">Search Products</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="product-search" class="form-control border-start-0" placeholder="Type product name or SKU..." autocomplete="off">
                    </div>

                    <!-- Search Results -->
                    <div id="search-results" class="list-group rounded shadow-sm border d-none" style="max-height: 280px; overflow-y: auto;">
                        <!-- Results injected via JS -->
                    </div>
                </div>
            </div>

            <!-- Transaction Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent pb-0">
                    <h5 class="fw-semibold text-accent m-0">Current Transaction</h5>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive border-0">
                        <table class="table align-middle" id="cart-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th style="width: 140px;">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-body">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted" id="cart-empty-message">Cart is empty. Add products above.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Summary -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm position-sticky" style="top: 90px;">
                <div class="card-header border-0 bg-transparent pb-0">
                    <h5 class="fw-semibold text-accent m-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold text-accent" id="summary-subtotal">₱0.00</span>
                    </div>
                    <hr class="text-muted my-3">
                    <div class="d-flex justify-content-between mb-4">
                        <h4 class="fw-bold text-accent">Total</h4>
                        <h4 class="fw-bold text-japanese-red" id="summary-total">₱0.00</h4>
                    </div>

                    <form id="checkout-form">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Amount Paid (₱)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-accent" id="amount-paid" placeholder="0.00" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted" style="font-size:13px;">Change</label>
                            <h3 class="fw-bold text-success" id="change-amount">₱0.00</h3>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold" id="submit-sale-btn" disabled>
                            <i class="bi bi-cart-check-fill me-2"></i> Record Sale
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold"><i class="bi bi-check-circle-fill text-success me-2"></i>Sale Recorded</h5>
            </div>
            <div class="modal-body text-center py-4">
                <p class="text-muted">The transaction has been successfully processed.</p>
                <div class="d-flex flex-column gap-2 mt-4 px-4">
                    <a href="" id="view-receipt-link" target="_blank" class="btn btn-primary">
                        <i class="bi bi-printer me-2"></i> View & Print Receipt
                    </a>
                    <button type="button" class="btn btn-light" onclick="window.location.reload();">
                        New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];

    const searchInput = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const cartBody = document.getElementById('cart-body');
    const cartEmptyMsg = document.getElementById('cart-empty-message');
    const subtotalText = document.getElementById('summary-subtotal');
    const totalText = document.getElementById('summary-total');
    const amountPaidInput = document.getElementById('amount-paid');
    const changeText = document.getElementById('change-amount');
    const submitBtn = document.getElementById('submit-sale-btn');

    // Handle AJAX search
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();
        if (query.length < 1) {
            searchResults.classList.add('d-none');
            return;
        }

        fetch(`{{ route('pos.products.search') }}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3';
                        btn.innerHTML = `
                            <div>
                                <span class="fw-semibold text-accent">${item.name}</span> <br>
                                <small class="text-muted">${item.sku} | Stock: ${item.stock}</small>
                            </div>
                            <span class="fw-bold text-japanese-red">₱${parseFloat(item.selling_price).toFixed(2)}</span>
                        `;
                        btn.addEventListener('click', () => {
                            addToCart(item);
                            searchResults.classList.add('d-none');
                            searchInput.value = '';
                        });
                        searchResults.appendChild(btn);
                    });
                    searchResults.classList.remove('d-none');
                } else {
                    searchResults.innerHTML = '<div class="list-group-item text-muted py-3">No products found</div>';
                    searchResults.classList.remove('d-none');
                }
            });
    });

    // Close search on click outside
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.classList.add('d-none');
        }
    });

    function addToCart(item) {
        if (item.stock < 1) {
            alert('This product is out of stock!');
            return;
        }

        const existing = cart.find(c => c.id === item.id);
        if (existing) {
            if (existing.quantity >= item.stock) {
                alert('Cannot add more than available stock.');
                return;
            }
            existing.quantity++;
        } else {
            cart.push({
                id: item.id,
                name: item.name,
                sku: item.sku,
                price: parseFloat(item.selling_price),
                stock: item.stock,
                quantity: 1
            });
        }
        renderCart();
    }

    function renderCart() {
        if (cart.length === 0) {
            cartEmptyMsg.style.display = 'table-row';
            cartBody.innerHTML = '';
            cartBody.appendChild(cartEmptyMsg);
            updateSummary(0);
            return;
        }

        cartEmptyMsg.style.display = 'none';
        cartBody.innerHTML = '';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <span class="fw-semibold text-accent">${item.name}</span> <br>
                    <small class="text-muted">${item.sku}</small>
                </td>
                <td>₱${item.price.toFixed(2)}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${index}, ${item.quantity - 1})">-</button>
                        <input type="number" class="form-control text-center" value="${item.quantity}" min="1" max="${item.stock}" onchange="updateQty(${index}, this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${index}, ${item.quantity + 1})">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold text-accent">₱${(item.price * item.quantity).toFixed(2)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            cartBody.appendChild(tr);
        });

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateSummary(total);
    }

    window.updateQty = function(index, qty) {
        qty = parseInt(qty);
        const item = cart[index];
        if (isNaN(qty) || qty < 1) qty = 1;
        if (qty > item.stock) {
            alert(`Maximum stock available is ${item.stock}`);
            qty = item.stock;
        }
        item.quantity = qty;
        renderCart();
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateSummary(total) {
        subtotalText.innerText = `₱${total.toFixed(2)}`;
        totalText.innerText = `₱${total.toFixed(2)}`;
        
        const paid = parseFloat(amountPaidInput.value);
        if (!isNaN(paid) && paid >= total && total > 0) {
            changeText.innerText = `₱${(paid - total).toFixed(2)}`;
            submitBtn.disabled = false;
        } else {
            changeText.innerText = `₱0.00`;
            submitBtn.disabled = true;
        }
    }

    amountPaidInput.addEventListener('input', function() {
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateSummary(total);
    });

    submitBtn.addEventListener('click', function() {
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const amountPaid = parseFloat(amountPaidInput.value);

        if (amountPaid < total) {
            alert('Insufficient payment.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';

        const payload = {
            items: cart.map(i => ({ product_id: i.id, quantity: i.quantity })),
            amount_paid: amountPaid
        };

        fetch(`{{ route('pos.sales.store') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Configure Receipt Modal
                const receiptUrl = `{{ url('/sales') }}/${data.sale_id}/receipt`;
                document.getElementById('view-receipt-link').href = receiptUrl;
                
                const myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                myModal.show();
            } else {
                alert(data.message || 'Error saving sale.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-cart-check-fill me-2"></i> Record Sale';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to save transaction.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-cart-check-fill me-2"></i> Record Sale';
        });
    });
</script>
@endsection
