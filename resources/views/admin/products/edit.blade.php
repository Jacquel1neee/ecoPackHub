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
                <div class="border rounded-3 p-3 mb-2 bg-light">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <button type="button" class="btn btn-outline-success btn-sm" id="ai-description-btn">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Generate Short Description from Image
                        </button>
                        <span class="text-muted small">You can still edit the text manually afterward.</span>
                    </div>
                    <div id="ai-description-status" class="text-muted small"></div>
                </div>
                <textarea id="product-description" name="description" rows="2" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount Price (RM)</label>
                    <input type="number" step="0.01" min="0" name="discount_price" class="form-control @error('discount_price') is-invalid @enderror" value="{{ old('discount_price', $product->discount_price) }}" placeholder="e.g., 12.50">
                    @error('discount_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount Percentage (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror" value="{{ old('discount_percentage', $product->discount_percentage) }}" placeholder="e.g., 10">
                    @error('discount_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_discount_active" value="1" class="form-check-input" id="is_discount_active" {{ old('is_discount_active', $product->is_discount_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_discount_active">
                            Activate Discount on Home Page
                        </label>
                    </div>
                </div>
            </div>

            <small class="text-muted d-block mb-2">Set either discount price or discount percentage. Tick activate to apply on homepage cards.</small>

            <div class="mb-3">
                <label class="form-label">Current Image</label>
                @if($product->image_url)
                    <div class="mb-2 border rounded-3 p-2 bg-light text-center">
                        <img id="current-product-image" src="{{ $product->image_url }}" style="max-width: 100%; max-height: 320px; width: auto; height: auto; object-fit: contain; border-radius: 8px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Leave empty to keep current image. Allowed: jpeg, png, jpg, gif (max 2MB)</small>
            </div>

            <div class="border rounded-3 p-3 mb-4 bg-light">
                <h5 class="fw-bold mb-2" style="color: var(--primary-green);">
                    <i class="fas fa-magic me-2"></i>AI Photo Enhancement
                </h5>
                <p class="text-muted small mb-3">
                    Upload a basic product photo and generate 4 polished variations. Pick the best one to set as the main image.
                </p>

                <div id="ai-enhance-form" class="row g-3">
                    <div class="col-md-8">
                        <input type="file" name="ai_image" id="ai-image-input" class="form-control" accept="image/*">
                        <small class="text-muted">Leave blank to use the current product image.</small>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-success w-100" id="ai-generate-btn">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Generate 4 Variations
                        </button>
                    </div>
                    <div class="col-12">
                        <div id="ai-status" class="text-muted small"></div>
                    </div>
                </div>

                <div id="ai-gallery" class="row g-3 mt-2"></div>
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

    document.getElementById('ai-description-btn').addEventListener('click', function() {
        const button = document.getElementById('ai-description-btn');
        const status = document.getElementById('ai-description-status');
        const descriptionField = document.getElementById('product-description');
        const aiImageInput = document.getElementById('ai-image-input');

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating...';
        status.className = 'text-muted small';
        status.textContent = 'Analyzing the image and drafting a short description...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (aiImageInput && aiImageInput.files && aiImageInput.files[0]) {
            formData.append('ai_image', aiImageInput.files[0]);
        }

        fetch('{{ route('admin.products.ai-description', $product) }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to generate a description.');
            }

            descriptionField.value = data.description;
            status.className = 'text-success small';
            status.textContent = 'Short description added. You can edit it manually if needed.';
        })
        .catch((error) => {
            status.className = 'text-danger small';
            status.textContent = error.message;
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-wand-magic-sparkles me-1"></i> Generate Short Description from Image';
        });
    });

    document.getElementById('ai-generate-btn').addEventListener('click', function() {
        const button = document.getElementById('ai-generate-btn');
        const status = document.getElementById('ai-status');
        const gallery = document.getElementById('ai-gallery');
        const input = document.getElementById('ai-image-input');

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating...';
        status.className = 'text-muted small';
        status.textContent = 'Creating polished variations...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (input.files && input.files[0]) {
            formData.append('ai_image', input.files[0]);
        }
        fetch('{{ route('admin.products.ai-enhance', $product) }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to generate images right now.');
            }

            gallery.innerHTML = '';
            data.images.forEach((image) => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                        <img src="${image.url}" class="img-fluid rounded mb-2" style="width: 100%; max-height: 260px; object-fit: contain; background: #f8f9fa;">
                        <button type="button" class="btn btn-green btn-sm select-ai-image" data-image-path="${image.path}">
                            <i class="fas fa-check"></i> Use as Main Image
                        </button>
                    </div>
                `;
                gallery.appendChild(col);
            });

            status.className = 'text-success small';
            status.textContent = data.message || 'Generated 4 variations.';
        })
        .catch((error) => {
            status.className = 'text-danger small';
            status.textContent = error.message;
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-wand-magic-sparkles me-1"></i> Generate 4 Variations';
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.select-ai-image')) {
            const button = e.target.closest('.select-ai-image');
            const imagePath = button.getAttribute('data-image-path');
            const status = document.getElementById('ai-status');

            status.className = 'text-muted small';
            status.textContent = 'Applying selected image...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('image_path', imagePath);

            fetch('{{ route('admin.products.ai-apply', $product) }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to apply the selected image.');
                }

                status.className = 'text-success small';
                status.textContent = 'Main image updated successfully.';
                const currentImagePreview = document.getElementById('current-product-image');
                if (currentImagePreview) {
                    currentImagePreview.src = data.image_url;
                }
                const currentImage = document.querySelector('input[name="image"]');
                if (currentImage) {
                    currentImage.value = '';
                }
            })
            .catch((error) => {
                status.className = 'text-danger small';
                status.textContent = error.message;
            });
        }
    });
</script>
@endsection