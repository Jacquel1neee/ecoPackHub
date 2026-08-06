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

            <div class="alert alert-info py-2 px-3 mb-3 small">
                <i class="fas fa-info-circle me-1"></i>
                Discounts are now set per variant below (each variant can have different discount settings).
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="show_price_on_homepage" value="1" class="form-check-input" id="show_price_on_homepage" {{ old('show_price_on_homepage', $product->show_price_on_homepage ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_price_on_homepage">
                    Show Price on Main Page
                </label>
            </div>

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
            <p class="text-muted small mb-3">Each variant uses a variant option only. Vendor quantity is set in the vendor admin screen, and vendor price is shown here as read-only while customer price stays editable.</p>

            <div id="variants-container" data-variant-count="{{ $product->variants->count() }}">
                @foreach($product->variants as $index => $variant)
                    <div class="variant-item row g-3 mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;">
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                        @php
                            $selectedVendorId = old('variants.'.$index.'.vendor_id', $variant->vendor_id);
                            if (!$selectedVendorId && $product->vendors->count() === 1) {
                                $selectedVendorId = $product->vendors->first()->id;
                            }

                            $siblingVariantForSelectedVendor = $selectedVendorId
                                ? $product->variants->first(function ($candidate) use ($variant, $selectedVendorId) {
                                    return (int) $candidate->id !== (int) $variant->id
                                        && (int) $candidate->vendor_id === (int) $selectedVendorId
                                        && $candidate->vendor_quantity !== null;
                                })
                                : null;

                            $currentVendorAssignment = $product->vendors->firstWhere('id', $selectedVendorId)
                                ?: ($variant->packing_quantity_option_id
                                    ? $product->vendors->firstWhere('pivot.packing_quantity_option_id', $variant->packing_quantity_option_id)
                                    : null)
                                ?: $product->vendors->first(function ($vendor) {
                                    return $vendor->pivot && $vendor->pivot->quantity !== null;
                                });

                            $currentVendorQuantity = old('variants.'.$index.'.vendor_quantity', $variant->vendor_quantity ?? $currentVendorAssignment?->pivot?->quantity ?? '');
                            if (($currentVendorQuantity === '' || $currentVendorQuantity === null) && $siblingVariantForSelectedVendor?->vendor_quantity !== null) {
                                $currentVendorQuantity = (int) $siblingVariantForSelectedVendor->vendor_quantity;
                            }
                            $currentOptionId = old('variants.'.$index.'.packing_quantity_option_id', $variant->packing_quantity_option_id ?? $currentVendorAssignment?->pivot?->packing_quantity_option_id);
                            if ((!$currentOptionId || $currentOptionId === '') && $siblingVariantForSelectedVendor?->packing_quantity_option_id) {
                                $currentOptionId = $siblingVariantForSelectedVendor->packing_quantity_option_id;
                            }
                            $matchingVendorOptionVariant = ($selectedVendorId && $currentOptionId)
                                ? $product->variants->first(function ($candidate) use ($variant, $selectedVendorId, $currentOptionId) {
                                    return (int) $candidate->id !== (int) $variant->id
                                        && (int) $candidate->vendor_id === (int) $selectedVendorId
                                        && (int) $candidate->packing_quantity_option_id === (int) $currentOptionId
                                        && $candidate->vendor_quantity !== null;
                                })
                                : null;
                            if (($currentVendorQuantity === '' || $currentVendorQuantity === null) && $matchingVendorOptionVariant?->vendor_quantity !== null) {
                                $currentVendorQuantity = (int) $matchingVendorOptionVariant->vendor_quantity;
                            }
                            if ($currentVendorQuantity === '' && preg_match('/\d+/', (string) $variant->packing_quantity, $qtyMatch)) {
                                $currentVendorQuantity = (int) $qtyMatch[0];
                            }
                            if ($currentVendorQuantity === '' || $currentVendorQuantity === null) {
                                $currentVendorQuantity = (int) ($variant->stock ?? 0);
                            }
                            $currentOptionName = $currentOptionId ? ($packingQuantityOptions->firstWhere('id', $currentOptionId)?->name ?? '') : '';
                            $currentQuantifierDisplay = trim(
                                ($currentVendorQuantity !== '' ? (string) ((int) $currentVendorQuantity) : '')
                                . ' '
                                . $currentOptionName
                            );
                        @endphp
                        <div class="col-md-1">
                            <label class="form-label small">Size</label>
                            @php
                                $currentSize = old('variants.'.$index.'.size', $variant->size);
                                $vendorSizeOptions = [];
                                if ($selectedVendorId) {
                                    foreach ($product->variants as $candidate) {
                                        if ((int) $candidate->vendor_id === (int) $selectedVendorId && !empty($candidate->size)) {
                                            $vendorSizeOptions[] = $candidate->size;
                                        }
                                    }
                                }
                                if ($currentSize && !in_array($currentSize, $vendorSizeOptions, true)) {
                                    $vendorSizeOptions[] = $currentSize;
                                }
                                $vendorSizeOptions = array_values(array_unique(array_filter($vendorSizeOptions)));
                            @endphp
                            <select name="variants[{{ $index }}][size]" class="form-select variant-size-select">
                                <option value="">Select Size</option>
                                @foreach($vendorSizeOptions as $sizeOption)
                                    <option value="{{ $sizeOption }}" {{ $currentSize === $sizeOption ? 'selected' : '' }}>{{ $sizeOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Quantifier (Vendor Qty + Option)</label>
                            <select class="form-select variant-quantifier-select">
                                <option value="">Select Quantifier</option>
                                @if($currentQuantifierDisplay !== '')
                                    <option value="{{ ($currentOptionId ?: '') . '|' . ($currentVendorQuantity !== '' && $currentVendorQuantity !== null ? (int) $currentVendorQuantity : '') . '|' . $currentOptionName }}" data-option-id="{{ $currentOptionId ?: '' }}" data-option-name="{{ $currentOptionName }}" data-quantity="{{ $currentVendorQuantity !== '' && $currentVendorQuantity !== null ? (int) $currentVendorQuantity : '' }}" selected>
                                        {{ $currentQuantifierDisplay }}
                                    </option>
                                @endif
                            </select>
                            <input type="hidden" name="variants[{{ $index }}][packing_quantity_option_id]" class="variant-packing-option-id" value="{{ $currentOptionId }}">
                            <input type="hidden" name="variants[{{ $index }}][packing_quantity]" class="variant-packing-quantity-value" value="{{ old('variants.'.$index.'.packing_quantity', $currentOptionName ?: ($variant->packingQuantityDisplay ?: $currentOptionName)) }}">
                            <input type="hidden" name="variants[{{ $index }}][vendor_quantity]" class="variant-vendor-quantity" value="{{ old('variants.'.$index.'.vendor_quantity', $currentVendorQuantity !== '' ? (int) $currentVendorQuantity : '') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Product Price (RM) *</label>
                            <input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control" 
                                   placeholder="45.00" value="{{ old('variants.'.$index.'.price', $variant->price) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Vendor *</label>
                            <select name="variants[{{ $index }}][vendor_id]" class="form-select variant-vendor-select" required>
                                @if($product->vendors->isEmpty())
                                    <option value="">No vendor assignment found. Please assign in Vendor page first.</option>
                                @else
                                    <option value="">Select Vendor</option>
                                    @foreach($product->vendors as $vendor)
                                        @php
                                            $variantAssignmentForVendor = $product->variants->first(function ($candidate) use ($variant, $vendor) {
                                                return (int) $candidate->id !== (int) $variant->id
                                                    && (int) $candidate->vendor_id === (int) $vendor->id
                                                    && (
                                                        $candidate->vendor_quantity !== null
                                                        || $candidate->packing_quantity_option_id !== null
                                                        || $candidate->vendor_price !== null
                                                    );
                                            });
                                            $isSelectedVendor = (int) $vendor->id === (int) $selectedVendorId;
                                            $vendorPrice = (int) $vendor->id === (int) $variant->vendor_id
                                                ? ($variant->vendor_price ?? ($vendor->pivot->price ?? ''))
                                                : ($variantAssignmentForVendor?->vendor_price ?? ($vendor->pivot->price ?? ''));
                                            $vendorQuantity = (int) $vendor->id === (int) $variant->vendor_id
                                                ? ($variant->vendor_quantity ?? ($vendor->pivot->quantity ?? ''))
                                                : ($variantAssignmentForVendor?->vendor_quantity ?? ($vendor->pivot->quantity ?? ''));
                                            if (($vendorQuantity === '' || $vendorQuantity === null) && $isSelectedVendor && $currentVendorQuantity !== '') {
                                                $vendorQuantity = $currentVendorQuantity;
                                            }
                                            if ($vendorQuantity === '' && (int) $vendor->id === (int) $variant->vendor_id && preg_match('/\d+/', (string) $variant->packing_quantity, $qtyMatch)) {
                                                $vendorQuantity = (int) $qtyMatch[0];
                                            }
                                            $vendorOptionId = (int) $vendor->id === (int) $variant->vendor_id
                                                ? ($variant->packing_quantity_option_id ?? ($vendor->pivot->packing_quantity_option_id ?? ''))
                                                : ($variantAssignmentForVendor?->packing_quantity_option_id ?? ($vendor->pivot->packing_quantity_option_id ?? ''));
                                            if (($vendorOptionId === '' || $vendorOptionId === null) && $isSelectedVendor && $currentOptionId) {
                                                $vendorOptionId = $currentOptionId;
                                            }
                                            $vendorVariantForOption = $vendorOptionId
                                                ? $product->variants->first(function ($candidate) use ($variant, $vendor, $vendorOptionId) {
                                                    return (int) $candidate->id !== (int) $variant->id
                                                        && (int) $candidate->vendor_id === (int) $vendor->id
                                                        && (int) $candidate->packing_quantity_option_id === (int) $vendorOptionId
                                                        && $candidate->vendor_quantity !== null;
                                                })
                                                : null;
                                            if (($vendorQuantity === '' || $vendorQuantity === null) && $vendorVariantForOption?->vendor_quantity !== null) {
                                                $vendorQuantity = (int) $vendorVariantForOption->vendor_quantity;
                                            }
                                            if (($vendorQuantity === '' || $vendorQuantity === null) && $isSelectedVendor) {
                                                $vendorQuantity = (int) ($variant->stock ?? 0);
                                            }
                                            $vendorOptionName = $vendorOptionId ? ($packingQuantityOptions->firstWhere('id', $vendorOptionId)?->name ?? '') : '';

                                            $vendorQuantifierOptions = [];
                                            if ($vendorOptionId || $vendorOptionName || $vendorQuantity !== '' || $vendorQuantity === 0) {
                                                $vendorQuantifierOptions[] = [
                                                    'option_id' => $vendorOptionId ? (int) $vendorOptionId : null,
                                                    'option_name' => $vendorOptionName,
                                                    'quantity' => $vendorQuantity !== '' && $vendorQuantity !== null ? (int) $vendorQuantity : null,
                                                ];
                                            }

                                            foreach ($product->variants as $candidate) {
                                                if ((int) $candidate->vendor_id !== (int) $vendor->id) {
                                                    continue;
                                                }

                                                $candidateOptionId = $candidate->packing_quantity_option_id;
                                                $candidateOptionName = $candidateOptionId
                                                    ? ($packingQuantityOptions->firstWhere('id', $candidateOptionId)?->name ?? '')
                                                    : ($candidate->packingQuantityDisplay ?? '');
                                                $candidateQty = $candidate->vendor_quantity;
                                                if (($candidateQty === null || $candidateQty === '') && preg_match('/\d+/', (string) $candidate->packing_quantity, $qtyMatch)) {
                                                    $candidateQty = (int) $qtyMatch[0];
                                                }

                                                if ($candidateOptionId || $candidateOptionName || $candidateQty !== null) {
                                                    $vendorQuantifierOptions[] = [
                                                        'option_id' => $candidateOptionId ? (int) $candidateOptionId : null,
                                                        'option_name' => $candidateOptionName,
                                                        'quantity' => $candidateQty !== null && $candidateQty !== '' ? (int) $candidateQty : null,
                                                    ];
                                                }
                                            }

                                            $vendorSizeOptions = $product->variants
                                                ->filter(fn ($candidate) => (int) $candidate->vendor_id === (int) $vendor->id && !empty($candidate->size))
                                                ->pluck('size')
                                                ->unique()
                                                ->values();
                                        @endphp
                                        <option value="{{ $vendor->id }}" data-price="{{ $vendorPrice }}" data-quantity="{{ $vendorQuantity }}" data-option-id="{{ $vendorOptionId }}" data-option-name="{{ $vendorOptionName }}" data-option-map='@json($vendorQuantifierOptions)' data-size-map='@json($vendorSizeOptions)' {{ $selectedVendorId == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Vendor Price (read-only)</label>
                            <input type="text" class="form-control variant-vendor-price-display" value="{{ old('variants.'.$index.'.vendor_price', $variant->vendor_price !== null ? number_format((float) $variant->vendor_price, 2, '.', '') : '') }}" readonly>
                            <input type="hidden" name="variants[{{ $index }}][vendor_price]" class="variant-vendor-price" value="{{ old('variants.'.$index.'.vendor_price', $variant->vendor_price) }}">
                        </div>
                        <div class="col-md-1">
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
                        <div class="col-12">
                            <div class="row g-2 mt-1">
                                <div class="col-md-3">
                                    <label class="form-label small">Variant Discount Price (RM)</label>
                                    <input type="number" step="0.01" min="0" name="variants[{{ $index }}][discount_price]" class="form-control" value="{{ old('variants.'.$index.'.discount_price', $variant->discount_price) }}" placeholder="e.g., 12.50">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Variant Discount Percentage (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" name="variants[{{ $index }}][discount_percentage]" class="form-control" value="{{ old('variants.'.$index.'.discount_percentage', $variant->discount_percentage) }}" placeholder="e.g., 10">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="variants[{{ $index }}][is_discount_active]" value="1" class="form-check-input" id="variant_discount_active_{{ $index }}" {{ old('variants.'.$index.'.is_discount_active', $variant->is_discount_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="variant_discount_active_{{ $index }}">Activate Variant Discount</label>
                                    </div>
                                </div>
                            </div>
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
    function initializeVariantRow(variantItem) {
        const vendorSelect = variantItem.querySelector('.variant-vendor-select');
        const priceInput = variantItem.querySelector('input[name*="[price]"]');
        const vendorPriceInput = variantItem.querySelector('.variant-vendor-price');
        const vendorPriceDisplay = variantItem.querySelector('.variant-vendor-price-display');
        const quantifierSelect = variantItem.querySelector('.variant-quantifier-select');
        const sizeSelect = variantItem.querySelector('.variant-size-select');
        const packingOptionIdInput = variantItem.querySelector('.variant-packing-option-id');
        const packingValueInput = variantItem.querySelector('.variant-packing-quantity-value');
        const vendorQuantityInput = variantItem.querySelector('.variant-vendor-quantity');

        const syncSizeChoices = () => {
            if (!vendorSelect || !sizeSelect) {
                return;
            }

            const selectedVendor = vendorSelect.options[vendorSelect.selectedIndex];
            let sizeOptions = [];
            try {
                sizeOptions = JSON.parse(selectedVendor?.dataset.sizeMap || '[]');
            } catch (error) {
                sizeOptions = [];
            }

            const currentSize = sizeSelect.value;
            const uniqueSizes = Array.from(new Set((sizeOptions || []).filter(Boolean)));
            if (currentSize && !uniqueSizes.includes(currentSize)) {
                uniqueSizes.unshift(currentSize);
            }

            sizeSelect.innerHTML = '<option value="">Select Size</option>';
            uniqueSizes.forEach((size) => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                sizeSelect.appendChild(option);
            });

            if (currentSize && uniqueSizes.includes(currentSize)) {
                sizeSelect.value = currentSize;
            } else if (uniqueSizes.length > 0) {
                sizeSelect.selectedIndex = 1;
            }
        };

        const parseVendorQuantifierOptions = (vendorOption) => {
            if (!vendorOption) {
                return [];
            }

            let mapped = [];
            try {
                mapped = JSON.parse(vendorOption.dataset.optionMap || '[]');
            } catch (error) {
                mapped = [];
            }

            const fallbackQty = vendorOption.dataset.quantity || '';
            const fallbackOptionId = vendorOption.dataset.optionId || '';
            const fallbackOptionName = vendorOption.dataset.optionName || '';

            if (mapped.length === 0 && (fallbackOptionId || fallbackOptionName || fallbackQty !== '')) {
                mapped.push({
                    option_id: fallbackOptionId !== '' ? Number(fallbackOptionId) : null,
                    option_name: fallbackOptionName,
                    quantity: fallbackQty !== '' ? Number(fallbackQty) : null,
                });
            }

            return mapped;
        };

        const applyQuantifierSelection = () => {
            if (!quantifierSelect || !packingValueInput || !packingOptionIdInput || !vendorQuantityInput) {
                return;
            }

            const selected = quantifierSelect.options[quantifierSelect.selectedIndex];
            const optionId = selected?.dataset.optionId || '';
            const optionName = selected?.dataset.optionName || '';
            const quantity = selected?.dataset.quantity || '';

            packingOptionIdInput.value = optionId;
            packingValueInput.value = optionName;
            vendorQuantityInput.value = quantity !== '' ? String(parseInt(quantity, 10)) : '';
        };

        const syncQuantifierChoices = () => {
            if (!vendorSelect || !quantifierSelect) {
                return;
            }

            const vendorOption = vendorSelect.options[vendorSelect.selectedIndex];
            const quantifierOptions = parseVendorQuantifierOptions(vendorOption);
            const currentOptionId = packingOptionIdInput?.value || '';
            const currentOptionName = packingValueInput?.value || '';
            const currentVendorQty = vendorQuantityInput?.value || '';

            const unique = new Map();
            quantifierOptions.forEach((item) => {
                const optionId = item.option_id ?? '';
                const optionName = item.option_name ?? '';
                const quantity = item.quantity ?? '';
                const key = `${optionId}|${quantity}|${optionName}`;
                if (!unique.has(key)) {
                    unique.set(key, { optionId, optionName, quantity });
                }
            });

            if (currentOptionId || currentOptionName || currentVendorQty !== '') {
                const currentKey = `${currentOptionId}|${currentVendorQty}|${currentOptionName}`;
                if (!unique.has(currentKey)) {
                    unique.set(currentKey, {
                        optionId: currentOptionId,
                        optionName: currentOptionName,
                        quantity: currentVendorQty,
                    });
                }
            }

            quantifierSelect.innerHTML = '<option value="">Select Quantifier</option>';

            unique.forEach((item, key) => {
                const optionElement = document.createElement('option');
                optionElement.value = key;
                optionElement.dataset.optionId = item.optionId;
                optionElement.dataset.optionName = item.optionName;
                optionElement.dataset.quantity = item.quantity;

                const qtyText = item.quantity !== '' && item.quantity !== null ? String(parseInt(item.quantity, 10)) : '';
                optionElement.textContent = [qtyText, item.optionName].filter(Boolean).join(' ').trim() || 'Custom';
                quantifierSelect.appendChild(optionElement);
            });

            const preferredKey = `${currentOptionId}|${currentVendorQty}|${currentOptionName}`;
            const hasPreferred = Array.from(quantifierSelect.options).some((option) => option.value === preferredKey);
            if (preferredKey !== '||' && hasPreferred) {
                quantifierSelect.value = preferredKey;
            } else if (quantifierSelect.options.length > 1) {
                quantifierSelect.selectedIndex = 1;
            }

            applyQuantifierSelection();
        };

        const syncVendorPrice = () => {
            if (!vendorSelect || !vendorPriceInput || !priceInput) {
                return;
            }

            const option = vendorSelect.options[vendorSelect.selectedIndex];
            const vendorPrice = option?.dataset.price || '';

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

        vendorSelect?.addEventListener('change', syncVendorPrice);
        priceInput?.addEventListener('input', syncVendorPrice);
        vendorSelect?.addEventListener('change', syncQuantifierChoices);
        vendorSelect?.addEventListener('change', syncSizeChoices);
        quantifierSelect?.addEventListener('change', applyQuantifierSelection);
        syncVendorPrice();
        syncQuantifierChoices();
        syncSizeChoices();
    }

    // ===== Variant Management =====
    const variantsContainer = document.getElementById('variants-container');
    let variantIndex = parseInt(variantsContainer?.dataset.variantCount || '0', 10);

    document.getElementById('add-variant-btn').addEventListener('click', function() {
        const container = document.getElementById('variants-container');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-item row g-3 mb-3 p-3';
        newVariant.style.cssText = 'background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0;';
        newVariant.innerHTML = `
            <div class="col-md-1">
                <label class="form-label small">Size</label>
                <select name="variants[${variantIndex}][size]" class="form-select variant-size-select">
                    <option value="">Select Size</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Quantifier (Vendor Qty + Option)</label>
                <select class="form-select variant-quantifier-select">
                    <option value="">Select Quantifier</option>
                </select>
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
                    @if($product->vendors->isEmpty())
                        <option value="">No vendor assignment found. Please assign in Vendor page first.</option>
                    @else
                        <option value="">Select Vendor</option>
                        @foreach($product->vendors as $vendor)
                            @php
                                $vendorPrice = $vendor->pivot->price ?? '';
                                $vendorQuantity = $vendor->pivot->quantity ?? '';
                                $vendorOptionId = $vendor->pivot->packing_quantity_option_id ?? '';
                                $vendorOptionName = $vendorOptionId ? ($packingQuantityOptions->firstWhere('id', $vendorOptionId)?->name ?? '') : '';
                                $vendorQuantifierOptions = [];
                                if ($vendorOptionId || $vendorOptionName || $vendorQuantity !== '') {
                                    $vendorQuantifierOptions[] = [
                                        'option_id' => $vendorOptionId ? (int) $vendorOptionId : null,
                                        'option_name' => $vendorOptionName,
                                        'quantity' => $vendorQuantity !== '' && $vendorQuantity !== null ? (int) $vendorQuantity : null,
                                    ];
                                }
                                foreach ($product->variants as $candidate) {
                                    if ((int) $candidate->vendor_id !== (int) $vendor->id) {
                                        continue;
                                    }
                                    $candidateOptionId = $candidate->packing_quantity_option_id;
                                    $candidateOptionName = $candidateOptionId
                                        ? ($packingQuantityOptions->firstWhere('id', $candidateOptionId)?->name ?? '')
                                        : ($candidate->packingQuantityDisplay ?? '');
                                    $candidateQty = $candidate->vendor_quantity;
                                    if (($candidateQty === null || $candidateQty === '') && preg_match('/\d+/', (string) $candidate->packing_quantity, $qtyMatch)) {
                                        $candidateQty = (int) $qtyMatch[0];
                                    }
                                    if ($candidateOptionId || $candidateOptionName || $candidateQty !== null) {
                                        $vendorQuantifierOptions[] = [
                                            'option_id' => $candidateOptionId ? (int) $candidateOptionId : null,
                                            'option_name' => $candidateOptionName,
                                            'quantity' => $candidateQty !== null && $candidateQty !== '' ? (int) $candidateQty : null,
                                        ];
                                    }
                                }

                                $vendorSizeOptions = $product->variants
                                    ->filter(fn ($candidate) => (int) $candidate->vendor_id === (int) $vendor->id && !empty($candidate->size))
                                    ->pluck('size')
                                    ->unique()
                                    ->values();
                            @endphp
                            <option value="{{ $vendor->id }}" data-price="{{ $vendorPrice }}" data-quantity="{{ $vendorQuantity }}" data-option-id="{{ $vendorOptionId }}" data-option-name="{{ $vendorOptionName }}" data-option-map='@json($vendorQuantifierOptions)' data-size-map='@json($vendorSizeOptions)'>{{ $vendor->name }}</option>
                        @endforeach
                    @endif
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
            <div class="col-12">
                <div class="row g-2 mt-1">
                    <div class="col-md-3">
                        <label class="form-label small">Variant Discount Price (RM)</label>
                        <input type="number" step="0.01" min="0" name="variants[${variantIndex}][discount_price]" class="form-control" placeholder="e.g., 12.50">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Variant Discount Percentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="variants[${variantIndex}][discount_percentage]" class="form-control" placeholder="e.g., 10">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="variants[${variantIndex}][is_discount_active]" value="1" class="form-check-input" id="variant_discount_active_${variantIndex}">
                            <label class="form-check-label" for="variant_discount_active_${variantIndex}">Activate Variant Discount</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newVariant);
        initializeVariantRow(newVariant);
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

        document.querySelectorAll('.variant-item').forEach((variantItem) => {
            initializeVariantRow(variantItem);
        });
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

        fetch("{{ route('admin.products.ai-description', $product) }}", {
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
        fetch("{{ route('admin.products.ai-enhance', $product) }}", {
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

            fetch("{{ route('admin.products.ai-apply', $product) }}", {
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
