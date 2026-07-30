@extends('layouts.app')

@section('title', 'Product Management')
@section('header_title', 'Product Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-semibold text-accent">Store Products</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </button>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Info</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>Main Stock</th>
                            <th>Bacon Stock</th>
                            <th>Gubat Stock</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid var(--border-color);">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px; font-size: 20px; border: 1px solid var(--border-color);">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-accent">{{ $product->name }}</span>
                                </td>
                                <td class="text-muted">{{ $product->sku }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>₱{{ number_format($product->buying_price, 2) }}</td>
                                <td class="fw-bold text-accent">₱{{ number_format($product->selling_price, 2) }}</td>
                                <td>
                                    @php $qty = $product->getStockInBranch(1); @endphp
                                    <span class="badge {{ $qty < 5 ? 'badge-low-stock' : 'bg-light text-dark border' }}">{{ $qty }}</span>
                                </td>
                                <td>
                                    @php $qty = $product->getStockInBranch(2); @endphp
                                    <span class="badge {{ $qty < 5 ? 'badge-low-stock' : 'bg-light text-dark border' }}">{{ $qty }}</span>
                                </td>
                                <td>
                                    @php $qty = $product->getStockInBranch(3); @endphp
                                    <span class="badge {{ $qty < 5 ? 'badge-low-stock' : 'bg-light text-dark border' }}">{{ $qty }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This will also clear all branch inventories for it.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Product Modal -->
                            <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-semibold">Edit Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">SKU</label>
                                                        <input type="text" class="form-control" name="sku" value="{{ $product->sku }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Category</label>
                                                        <select class="form-select" name="category_id" required>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Product Image</label>
                                                        <input type="file" class="form-control" name="image">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Buying Price (₱)</label>
                                                        <input type="number" step="0.01" class="form-control" name="buying_price" value="{{ $product->buying_price }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Selling Price (₱)</label>
                                                        <input type="number" step="0.01" class="form-control" name="selling_price" value="{{ $product->selling_price }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Minoyaki Ramen Bowl">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku" required placeholder="e.g. MINO-RAM-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Buying Price (₱)</label>
                            <input type="number" step="0.01" class="form-control" name="buying_price" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Selling Price (₱)</label>
                            <input type="number" step="0.01" class="form-control" name="selling_price" required placeholder="0.00">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Initial Quantity (Adds directly to Main Branch)</label>
                            <input type="number" class="form-control" name="quantity" required value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
