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

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount Price (RM)</label>
                    <input type="number" step="0.01" min="0" name="discount_price" class="form-control @error('discount_price') is-invalid @enderror" value="{{ old('discount_price') }}" placeholder="e.g., 12.50">
                    @error('discount_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount Percentage (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror" value="{{ old('discount_percentage') }}" placeholder="e.g., 10">
                    @error('discount_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_discount_active" value="1" class="form-check-input" id="is_discount_active" {{ old('is_discount_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_discount_active">
                            Activate Discount on Home Page
                        </label>
                    </div>
                </div>
            </div>

            <small class="text-muted d-block mb-2">Set either discount price or discount percentage. Tick activate to apply on homepage cards.</small>

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
            <p class="text-muted small mb-3">Each variant has one vendor price (cost) and one product price (selling price).</p>

            <div id="variants-container">
                <div class="variant-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                    <div class="col-md-1">
                        <label class="form-label small">Size</label>
                        <input type="text" name="variants[0][size]" class="form-control" placeholder="e.g., 600ml, 9inch" value="Standard">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Packing Quantity *</label>
                        <input type="text" name="variants[0][packing_quantity]" class="form-control" placeholder="e.g., 400 pcs/ctn" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Product Price (RM) *</label>
                        <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="45.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Vendor *</label>
                        <select name="variants[0][vendor_id]" class="form-select" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('variants.0.vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Vendor Price (RM) *</label>
                        <input type="number" step="0.01" name="variants[0][vendor_price]" class="form-control" placeholder="30.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Stock *</label>
                        <input type="number" name="variants[0][stock]" class="form-control" placeholder="100" value="0" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
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
            <div class="col-md-1">
                <label class="form-label small">Size</label>
                <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="e.g., 600ml, 9inch" value="Standard">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Packing Quantity *</label>
                <input type="text" name="variants[${variantIndex}][packing_quantity]" class="form-control" placeholder="e.g., 400 pcs/ctn" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Product Price (RM) *</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="45.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Vendor *</label>
                <select name="variants[${variantIndex}][vendor_id]" class="form-select" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Vendor Price (RM) *</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][vendor_price]" class="form-control" placeholder="30.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Stock *</label>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="100" value="0" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100 remove-variant">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;
        container.appendChild(newVariant);
        variantIndex++;

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

    // ===== Initialize =====
    document.addEventListener('DOMContentLoaded', function() {
        // Variants: hide remove button if only one
        const firstVariantRemove = document.querySelector('.remove-variant');
        if (firstVariantRemove) {
            firstVariantRemove.style.display = 'none';
        }

    });
</script>
@endsection