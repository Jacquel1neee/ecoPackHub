@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-edit me-2"></i>Edit Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-custom">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Product Basic Info -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Code *</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                           value="{{ old('code', $product->code) }}" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Material</label>
                    <input type="text" name="material" class="form-control" value="{{ old('material', $product->material) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="2" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Current Image</label>
                @if($product->image_url)
                    <div class="mb-2">
                        <img src="{{ $product->image_url }}" style="max-height: 150px; border-radius: 8px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Leave empty to keep current image. Allowed: jpeg, png, jpg, gif (max 2MB)</small>
            </div>

            <hr class="my-4">
            
            <!-- ===== VARIANTS SECTION ===== -->
            <h5 class="fw-bold mb-3" style="color: var(--primary-green);">
                <i class="fas fa-layer-group me-2"></i>Product Variants (Sizes/Prices)
            </h5>
            <p class="text-muted small mb-3">Each variant has one vendor price (cost) and one product price (selling price).</p>

            <div id="variants-container">
                @foreach($product->variants as $index => $variant)
                    <div class="variant-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                        <div class="col-md-1">
                            <label class="form-label small">Size</label>
                            <input type="text" name="variants[{{ $index }}][size]" class="form-control" 
                                   placeholder="e.g., 600ml, 9inch" value="{{ old('variants.'.$index.'.size', $variant->size) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Packing Quantity *</label>
                            <input type="text" name="variants[{{ $index }}][packing_quantity]" class="form-control" 
                                   placeholder="e.g., 400 pcs/ctn" value="{{ old('variants.'.$index.'.packing_quantity', $variant->packing_quantity) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Product Price (RM) *</label>
                            <input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control" 
                                   placeholder="45.00" value="{{ old('variants.'.$index.'.price', $variant->price) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Vendor *</label>
                            <select name="variants[{{ $index }}][vendor_id]" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('variants.'.$index.'.vendor_id', $variant->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Vendor Price (RM) *</label>
                            <input type="number" step="0.01" name="variants[{{ $index }}][vendor_price]" class="form-control" 
                                   placeholder="30.00" value="{{ old('variants.'.$index.'.vendor_price', $variant->vendor_price) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Stock *</label>
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control" 
                                   placeholder="100" value="{{ old('variants.'.$index.'.stock', $variant->stock) }}" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-variant" 
                                    {{ $loop->count <= 1 ? 'style="display:none;"' : '' }}>
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-variant-btn" class="btn btn-outline-success btn-sm mt-2">
                <i class="fas fa-plus me-1"></i> Add Another Variant
            </button>

            <hr class="my-4">

            <button type="submit" class="btn btn-green">
                <i class="fas fa-save"></i> Update Product
            </button>
        </form>
    </div>
</div>

<script>
    // ===== Variant Management =====
    let variantIndex = {{ $product->variants->count() }};

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

    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelectorAll('.variant-item').length === 1) {
            const btn = document.querySelector('.remove-variant');
            if (btn) btn.style.display = 'none';
        }
    });
</script>
@endsection