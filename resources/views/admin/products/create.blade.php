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
                <div class="border rounded-3 p-3 mb-2 bg-light">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <button type="button" class="btn btn-outline-success btn-sm" id="ai-description-btn">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Generate Short Description from Image
                        </button>
                        <span class="text-muted small">You can still edit the text manually afterward.</span>
                    </div>
                    <div id="ai-description-status" class="text-muted small"></div>
                </div>
                <textarea id="product-description" name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
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

            <div class="mb-3 form-check">
                <input type="checkbox" name="show_price_on_homepage" value="1" class="form-check-input" id="show_price_on_homepage" {{ old('show_price_on_homepage', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_price_on_homepage">
                    Show Price on Main Page
                </label>
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
            <p class="text-muted small mb-3">Each variant uses a variant option only. Vendor quantity is set in the vendor admin screen, and vendor price is shown here as read-only while customer price stays editable.</p>

            <div id="variants-container">
                <div class="variant-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                    <div class="col-md-1">
                        <label class="form-label small">Size</label>
                        <input type="text" name="variants[0][size]" class="form-control" placeholder="e.g., 600ml, 9inch" value="Standard">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Quantifier (Vendor Qty + Option)</label>
                        <input type="text" class="form-control variant-quantifier-display" value="" placeholder="Set in Vendor page" readonly>
                        <input type="hidden" name="variants[0][packing_quantity_option_id]" class="variant-packing-option-id" value="{{ old('variants.0.packing_quantity_option_id') }}">
                        <input type="hidden" name="variants[0][packing_quantity]" class="variant-packing-quantity-value" value="{{ old('variants.0.packing_quantity') }}">
                        <input type="hidden" name="variants[0][vendor_quantity]" class="variant-vendor-quantity" value="{{ old('variants.0.vendor_quantity') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Product Price (RM) *</label>
                        <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="45.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Vendor *</label>
                        <select name="variants[0][vendor_id]" class="form-select variant-vendor-select" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-price="" data-quantity="" data-option-id="" data-option-name="">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Vendor Price (read-only)</label>
                        <input type="text" class="form-control variant-vendor-price-display" value="" readonly>
                        <input type="hidden" name="variants[0][vendor_price]" class="variant-vendor-price" value="">
                    </div>
                    <div class="col-md-1">
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
    function initializeVariantRow(variantItem) {
        const vendorSelect = variantItem.querySelector('.variant-vendor-select');
        const priceInput = variantItem.querySelector('input[name*="[price]"]');
        const vendorPriceInput = variantItem.querySelector('.variant-vendor-price');
        const vendorPriceDisplay = variantItem.querySelector('.variant-vendor-price-display');
        const quantifierDisplay = variantItem.querySelector('.variant-quantifier-display');
        const packingOptionIdInput = variantItem.querySelector('.variant-packing-option-id');
        const packingValueInput = variantItem.querySelector('.variant-packing-quantity-value');
        const vendorQuantityInput = variantItem.querySelector('.variant-vendor-quantity');

        const syncVendorPrice = () => {
            if (!vendorSelect || !vendorPriceInput || !priceInput) {
                return;
            }

            const option = vendorSelect.options[vendorSelect.selectedIndex];
            const vendorPrice = option?.dataset.price || priceInput.value || '';

            if (!vendorSelect.value) {
                return;
            }

            if (!vendorPrice && (vendorPriceInput.value || vendorPriceDisplay?.value)) {
                return;
            }

            vendorPriceInput.value = vendorPrice;
            if (vendorPriceDisplay) {
                vendorPriceDisplay.value = vendorPrice ? Number(vendorPrice).toFixed(2) : '';
            }
        };

        const syncQuantifier = () => {
            if (!vendorSelect || !packingValueInput) {
                return;
            }

            const option = vendorSelect.options[vendorSelect.selectedIndex];
            const vendorQuantity = option?.dataset.quantity || '';
            const optionId = option?.dataset.optionId || '';
            const optionName = option?.dataset.optionName || '';
            const quantifier = [vendorQuantity, optionName].filter(Boolean).join(' ');

            if (!vendorSelect.value) {
                return;
            }

            if (!quantifier && !optionId && !optionName && (quantifierDisplay?.value || packingValueInput.value)) {
                return;
            }

            if (quantifierDisplay) {
                quantifierDisplay.value = quantifier;
            }
            if (packingOptionIdInput) {
                packingOptionIdInput.value = optionId;
            }
            if (vendorQuantityInput) {
                vendorQuantityInput.value = vendorQuantity !== '' ? String(parseInt(vendorQuantity, 10)) : '';
            }
            packingValueInput.value = optionName;
        };

        vendorSelect?.addEventListener('change', syncVendorPrice);
        priceInput?.addEventListener('input', syncVendorPrice);
        vendorSelect?.addEventListener('change', syncQuantifier);
        syncVendorPrice();
        syncQuantifier();
    }

    document.getElementById('ai-description-btn').addEventListener('click', function() {
        const button = document.getElementById('ai-description-btn');
        const status = document.getElementById('ai-description-status');
        const descriptionField = document.getElementById('product-description');
        const imageInput = document.querySelector('input[name="image"]');

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating...';
        status.className = 'text-muted small';
        status.textContent = 'Analyzing the image and drafting a short description...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (imageInput && imageInput.files && imageInput.files[0]) {
            formData.append('ai_image', imageInput.files[0]);
        }

        fetch("{{ route('admin.products.ai-description') }}", {
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
            <div class="col-md-3">
                <label class="form-label small">Quantifier (Vendor Qty + Option)</label>
                <input type="text" class="form-control variant-quantifier-display" value="" placeholder="Set in Vendor page" readonly>
                <input type="hidden" name="variants[${variantIndex}][packing_quantity_option_id]" class="variant-packing-option-id" value="">
                <input type="hidden" name="variants[${variantIndex}][packing_quantity]" class="variant-packing-quantity-value" value="">
                <input type="hidden" name="variants[${variantIndex}][vendor_quantity]" class="variant-vendor-quantity" value="">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Product Price (RM) *</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="45.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Vendor *</label>
                <select name="variants[${variantIndex}][vendor_id]" class="form-select variant-vendor-select" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" data-price="" data-quantity="" data-option-id="" data-option-name="">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Vendor Price (read-only)</label>
                <input type="text" class="form-control variant-vendor-price-display" value="" readonly>
                <input type="hidden" name="variants[${variantIndex}][vendor_price]" class="variant-vendor-price" value="">
            </div>
            <div class="col-md-1">
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
        initializeVariantRow(newVariant);
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

        document.querySelectorAll('.variant-item').forEach((variantItem) => {
            initializeVariantRow(variantItem);
        });

    });
</script>
@endsection