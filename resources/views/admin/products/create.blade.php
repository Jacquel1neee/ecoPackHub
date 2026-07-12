@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-plus me-2"></i>Add New Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-custom">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- ===== Product Basic Info ===== -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Code *</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Material</label>
                    <input type="text" name="material" class="form-control" value="{{ old('material') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Allowed: jpeg, png, jpg, gif (max 2MB)</small>
            </div>

            <hr class="my-4">
            
            <!-- ===== VARIANTS SECTION ===== -->
            <h5 class="fw-bold mb-3" style="color: var(--primary-green);">
                <i class="fas fa-layer-group me-2"></i>Product Variants (Sizes/Prices)
            </h5>
            <p class="text-muted small mb-3">Add different sizes/options for this product. Each variant has its own price and stock.</p>

            <div id="variants-container">
                <div class="variant-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                    <div class="col-md-3">
                        <label class="form-label small">Size</label>
                        <input type="text" name="variants[0][size]" class="form-control" placeholder="e.g., 600ml, 9inch" value="Standard">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Packing Quantity *</label>
                        <input type="text" name="variants[0][packing_quantity]" class="form-control" placeholder="e.g., 400 pcs/ctn" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Price (RM) *</label>
                        <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="45.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Stock *</label>
                        <input type="number" name="variants[0][stock]" class="form-control" placeholder="100" value="0" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-variant" style="display:none;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-variant-btn" class="btn btn-outline-success btn-sm mt-2">
                <i class="fas fa-plus me-1"></i> Add Another Variant
            </button>

            <hr class="my-4">

            <!-- ===== VENDORS SECTION ===== -->
            <h5 class="fw-bold mb-3" style="color: var(--primary-green);">
                <i class="fas fa-truck me-2"></i>Vendors & Prices
            </h5>
            <p class="text-muted small mb-3">Assign vendors who supply this product and their prices.</p>

            @if(isset($vendors) && $vendors->count() > 0)
                <div id="vendors-container">
                    <div class="vendor-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                        <div class="col-md-5">
                            <label class="form-label small">Vendor *</label>
                            <select name="vendors[0][id]" class="form-select vendor-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendors.0.id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Price (RM) *</label>
                            <input type="number" step="0.01" name="vendors[0][price]" class="form-control" placeholder="0.00" value="{{ old('vendors.0.price') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Preferred</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="vendors[0][is_preferred]" value="1" class="form-check-input" {{ old('vendors.0.is_preferred') ? 'checked' : '' }}>
                                <label class="form-check-label small">★</label>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-vendor" style="display:none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-vendor-btn" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-plus me-1"></i> Add Another Vendor
                </button>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No vendors found. Please <a href="{{ route('admin.vendors.create') }}">add a vendor</a> first.
                </div>
            @endif

            <hr class="my-4">

            <button type="submit" class="btn btn-green">
                <i class="fas fa-save"></i> Save Product
            </button>
        </form>
    </div>
</div>

<script>
    // ===== Variant Management =====
    let variantIndex = 1;

    document.getElementById('add-variant-btn').addEventListener('click', function() {
        const container = document.getElementById('variants-container');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-item row g-3 mb-3 p-3';
        newVariant.style.cssText = 'background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;';
        newVariant.innerHTML = `
            <div class="col-md-3">
                <label class="form-label small">Size</label>
                <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="e.g., 600ml, 9inch" value="Standard">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Packing Quantity *</label>
                <input type="text" name="variants[${variantIndex}][packing_quantity]" class="form-control" placeholder="e.g., 400 pcs/ctn" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Price (RM) *</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="45.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Stock *</label>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="100" value="0" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100 remove-variant">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;
        container.appendChild(newVariant);
        variantIndex++;

        // Show all remove buttons
        document.querySelectorAll('.remove-variant').forEach(function(btn) {
            btn.style.display = 'block';
        });
    });

    // Remove variant handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variant')) {
            const button = e.target.closest('.remove-variant');
            const variantItem = button.closest('.variant-item');
            const container = document.getElementById('variants-container');
            
            if (container.querySelectorAll('.variant-item').length > 1) {
                variantItem.remove();
            } else {
                alert('You need at least one variant.');
            }
            
            if (container.querySelectorAll('.variant-item').length === 1) {
                const btn = document.querySelector('.remove-variant');
                if (btn) btn.style.display = 'none';
            }
        }
    });

    // ===== Vendor Management =====
    let vendorIndex = 1;

    @if(isset($vendors) && $vendors->count() > 0)
    // Add vendor button
    document.getElementById('add-vendor-btn').addEventListener('click', function() {
        const container = document.getElementById('vendors-container');
        const newVendor = document.createElement('div');
        newVendor.className = 'vendor-item row g-3 mb-3 p-3';
        newVendor.style.cssText = 'background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;';
        newVendor.innerHTML = `
            <div class="col-md-5">
                <label class="form-label small">Vendor *</label>
                <select name="vendors[${vendorIndex}][id]" class="form-select vendor-select" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Price (RM) *</label>
                <input type="number" step="0.01" name="vendors[${vendorIndex}][price]" class="form-control" placeholder="0.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Preferred</label>
                <div class="form-check mt-2">
                    <input type="checkbox" name="vendors[${vendorIndex}][is_preferred]" value="1" class="form-check-input">
                    <label class="form-check-label small">★</label>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100 remove-vendor">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newVendor);
        vendorIndex++;

        // Show all remove buttons
        document.querySelectorAll('.remove-vendor').forEach(function(btn) {
            btn.style.display = 'block';
        });
    });

    // Remove vendor handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-vendor')) {
            const button = e.target.closest('.remove-vendor');
            const vendorItem = button.closest('.vendor-item');
            const container = document.getElementById('vendors-container');
            
            if (container.querySelectorAll('.vendor-item').length > 1) {
                vendorItem.remove();
            } else {
                alert('You need at least one vendor.');
            }
            
            if (container.querySelectorAll('.vendor-item').length === 1) {
                const btn = document.querySelector('.remove-vendor');
                if (btn) btn.style.display = 'none';
            }
        }
    });
    @endif

    // ===== Initialize =====
    document.addEventListener('DOMContentLoaded', function() {
        // Variants: hide remove button if only one
        const firstVariantRemove = document.querySelector('.remove-variant');
        if (firstVariantRemove) {
            firstVariantRemove.style.display = 'none';
        }

        // Vendors: hide remove button if only one
        const firstVendorRemove = document.querySelector('.remove-vendor');
        if (firstVendorRemove) {
            firstVendorRemove.style.display = 'none';
        }
    });
</script>
@endsection