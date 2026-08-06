@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-edit me-2"></i>Edit Vendor
        </h2>
        <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.vendors.update', $vendor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Vendor Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $vendor->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                   value="{{ old('contact_person', $vendor->contact_person) }}">
                            @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $vendor->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $vendor->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $vendor->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                                   id="is_active" {{ $vendor->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                @php
                    $assignedProducts = $vendor->products->keyBy('id');
                    $assignedVariantMap = collect();
                    foreach ($vendor->products as $assignedProduct) {
                        foreach ($assignedProduct->variants as $assignedVariant) {
                            if ((int) $assignedVariant->vendor_id === (int) $vendor->id) {
                                $assignedVariantMap->put($assignedVariant->id, $assignedVariant);
                            }
                        }
                    }

                    $assignedVariantIds = $assignedVariantMap->keys()->map(fn ($id) => (int) $id)->values();
                    $oldVariantRows = old('variants', []);
                    $oldCustomVariantRows = collect($oldVariantRows)
                        ->filter(fn ($row, $key) => !is_numeric($key))
                        ->values();

                    $allVariantsById = $products
                        ->flatMap(fn ($product) => $product->variants)
                        ->keyBy('id');

                    $preloadedProductIds = collect(array_keys($oldVariantRows))
                        ->map(fn ($variantId) => (int) $variantId)
                        ->map(fn ($variantId) => optional($allVariantsById->get($variantId))->product_id)
                        ->filter()
                        ->map(fn ($productId) => (int) $productId)
                        ->unique()
                        ->values();
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1 fw-bold" style="color: var(--primary-green);">Assigned Product Variants</h5>
                        <p class="text-muted small mb-0">This section shows what this vendor already supplies.</p>
                    </div>
                    <button type="button" class="btn btn-outline-success" id="addProductToggleBtn">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>

                @if($assignedVariantMap->isEmpty())
                    <div class="alert alert-light border mb-4">
                        <i class="fas fa-info-circle me-2 text-muted"></i>
                        <span class="text-muted">No variants assigned yet. Click <strong>Add Product</strong> to add variants for this vendor.</span>
                    </div>
                @else
                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Use</th>
                                    <th>Product</th>
                                    <th style="width: 140px;">Variant Size</th>
                                    <th style="width: 160px;">Quantity</th>
                                    <th style="width: 180px;">Variant Option</th>
                                    <th style="width: 180px;">Vendor Price (RM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    @php
                                        $assignedVariantsForProduct = $product->variants
                                            ->filter(fn ($variant) => $assignedVariantIds->contains((int) $variant->id))
                                            ->values();
                                    @endphp

                                    @if($assignedVariantsForProduct->isEmpty())
                                        @continue
                                    @endif

                                    @foreach($assignedVariantsForProduct as $variantIndex => $variant)
                                        @php
                                            $variantAssignment = $assignedVariantMap->get($variant->id);
                                            $fallbackProductAssignment = $assignedProducts->get($product->id)?->pivot;

                                            $oldRow = $oldVariantRows[$variant->id] ?? [];
                                            $isChecked = filter_var($oldRow['selected'] ?? true, FILTER_VALIDATE_BOOLEAN);
                                            $quantityValue = old('variants.' . $variant->id . '.quantity', $variantAssignment->vendor_quantity ?? $fallbackProductAssignment->quantity ?? '');
                                            $priceValue = old('variants.' . $variant->id . '.price', $variantAssignment->vendor_price ?? $fallbackProductAssignment->price ?? '');
                                            $optionValue = old('variants.' . $variant->id . '.packing_quantity_option_id', $variantAssignment->packing_quantity_option_id ?? $fallbackProductAssignment->packing_quantity_option_id ?? '');
                                        @endphp

                                        <tr class="variant-row" data-variant-id="{{ $variant->id }}">
                                            <td>
                                                <input type="checkbox" name="variants[{{ $variant->id }}][selected]" value="1" class="form-check-input variant-select" data-variant-id="{{ $variant->id }}" {{ $isChecked ? 'checked' : '' }}>
                                            </td>

                                            @if($variantIndex === 0)
                                                <td rowspan="{{ $assignedVariantsForProduct->count() }}" class="align-top">
                                                    <strong>{{ $product->name }}</strong><br>
                                                    <small class="text-muted">{{ $product->code }}</small>
                                                </td>
                                            @endif

                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $variant->size ?: 'Default' }}</span>
                                            </td>
                                            <td>
                                                <input type="number" step="1" min="0" name="variants[{{ $variant->id }}][quantity]" class="form-control form-control-sm variant-field" data-variant-id="{{ $variant->id }}" value="{{ $quantityValue }}" placeholder="100">
                                            </td>
                                            <td>
                                                <select name="variants[{{ $variant->id }}][packing_quantity_option_id]" class="form-select form-select-sm variant-field" data-variant-id="{{ $variant->id }}">
                                                    <option value="">Select Variant Option</option>
                                                    @foreach($packingQuantityOptions as $option)
                                                        <option value="{{ $option->id }}" {{ $optionValue == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="variants[{{ $variant->id }}][price]" class="form-control form-control-sm variant-field" data-variant-id="{{ $variant->id }}" value="{{ $priceValue }}" placeholder="0.00">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div id="addProductPanel" class="card border-success mb-4 d-none">
                    <div class="card-body">
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-8">
                                <label for="productPicker" class="form-label fw-semibold mb-1">Choose Product to Add</label>
                                <select id="productPicker" class="form-select">
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        @php
                                            $availableVariantCount = $product->variants
                                                ->filter(fn ($variant) => ! $assignedVariantIds->contains((int) $variant->id))
                                                ->count();
                                            $isPreloaded = $preloadedProductIds->contains((int) $product->id);
                                        @endphp
                                        @if($availableVariantCount > 0)
                                            <option value="{{ $product->id }}" {{ $isPreloaded ? 'disabled' : '' }}>
                                                {{ $product->name }} ({{ $product->code }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="addSelectedProductBtn" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-2"></i>Add Selected Product
                                </button>
                            </div>
                        </div>

                        <div id="productAddCardsContainer" class="d-grid gap-3">
                            @foreach($products as $product)
                                @php
                                    $availableVariants = $product->variants
                                        ->filter(fn ($variant) => ! $assignedVariantIds->contains((int) $variant->id))
                                        ->values();
                                    $showCardByDefault = $preloadedProductIds->contains((int) $product->id);
                                @endphp

                                @if($availableVariants->isEmpty())
                                    @continue
                                @endif

                                <div class="card product-add-card {{ $showCardByDefault ? '' : 'd-none' }}" data-product-id="{{ $product->id }}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <small class="text-muted ms-2">{{ $product->code }}</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-product-btn" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-times me-1"></i>Remove
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0 align-middle">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 90px;">Use</th>
                                                        <th style="width: 140px;">Variant Size</th>
                                                        <th style="width: 160px;">Quantity</th>
                                                        <th style="width: 180px;">Variant Option</th>
                                                        <th style="width: 180px;">Vendor Price (RM)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($availableVariants as $variant)
                                                        @php
                                                            $oldRow = $oldVariantRows[$variant->id] ?? [];
                                                            $hasAnyOldValue = (($oldRow['quantity'] ?? '') !== '')
                                                                || (($oldRow['packing_quantity_option_id'] ?? '') !== '')
                                                                || (($oldRow['price'] ?? '') !== '');
                                                            $isChecked = filter_var($oldRow['selected'] ?? false, FILTER_VALIDATE_BOOLEAN) || $hasAnyOldValue;
                                                        @endphp
                                                        <tr class="variant-row" data-variant-id="{{ $variant->id }}">
                                                            <td>
                                                                <input type="checkbox" name="variants[{{ $variant->id }}][selected]" value="1" class="form-check-input variant-select" data-variant-id="{{ $variant->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border">{{ $variant->size ?: 'Default' }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="1" min="0" name="variants[{{ $variant->id }}][quantity]" class="form-control form-control-sm variant-field" data-variant-id="{{ $variant->id }}" value="{{ old('variants.' . $variant->id . '.quantity') }}" placeholder="100">
                                                            </td>
                                                            <td>
                                                                <select name="variants[{{ $variant->id }}][packing_quantity_option_id]" class="form-select form-select-sm variant-field" data-variant-id="{{ $variant->id }}">
                                                                    <option value="">Select Variant Option</option>
                                                                    @foreach($packingQuantityOptions as $option)
                                                                        <option value="{{ $option->id }}" {{ old('variants.' . $variant->id . '.packing_quantity_option_id') == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" name="variants[{{ $variant->id }}][price]" class="form-control form-control-sm variant-field" data-variant-id="{{ $variant->id }}" value="{{ old('variants.' . $variant->id . '.price') }}" placeholder="0.00">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-success mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1 fw-bold" style="color: var(--primary-green);">Add Product Variant Size (Vendor Provided)</h6>
                                <p class="text-muted small mb-0">Add a new variant size for a product under this vendor, then admin can select it in product edit.</p>
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm" id="addCustomVariantBtn">
                                <i class="fas fa-plus me-1"></i>Add Variant Size
                            </button>
                        </div>

                        <div id="customVariantRows" class="d-grid gap-2" data-initial-count="{{ $oldCustomVariantRows->count() }}">
                            @foreach($oldCustomVariantRows as $customIndex => $customRow)
                                <div class="row g-2 custom-variant-row align-items-end border rounded p-2">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Product</label>
                                        <select name="variants[new_custom_{{ $customIndex }}][product_id]" class="form-select form-select-sm">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ (int) ($customRow['product_id'] ?? 0) === (int) $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Variant Size</label>
                                        <input type="text" name="variants[new_custom_{{ $customIndex }}][size]" class="form-control form-control-sm" value="{{ $customRow['size'] ?? '' }}" placeholder="e.g. 12oz / Large">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Quantity</label>
                                        <input type="number" min="0" step="1" name="variants[new_custom_{{ $customIndex }}][quantity]" class="form-control form-control-sm" value="{{ $customRow['quantity'] ?? '' }}" placeholder="100">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Variant Option</label>
                                        <select name="variants[new_custom_{{ $customIndex }}][packing_quantity_option_id]" class="form-select form-select-sm">
                                            <option value="">Select Option</option>
                                            @foreach($packingQuantityOptions as $option)
                                                <option value="{{ $option->id }}" {{ (string) ($customRow['packing_quantity_option_id'] ?? '') === (string) $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Vendor Price (RM)</label>
                                        <input type="number" min="0" step="0.01" name="variants[new_custom_{{ $customIndex }}][price]" class="form-control form-control-sm" value="{{ $customRow['price'] ?? '' }}" placeholder="0.00">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-custom-variant-btn"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <input type="hidden" name="variants[new_custom_{{ $customIndex }}][selected]" value="1">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn" style="background-color: var(--primary-green); color: #fff;">
                    <i class="fas fa-save me-2"></i>Update Vendor
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const addProductPanel = document.getElementById('addProductPanel');
    const addProductToggleBtn = document.getElementById('addProductToggleBtn');
    const addSelectedProductBtn = document.getElementById('addSelectedProductBtn');
    const productPicker = document.getElementById('productPicker');
    const customVariantRows = document.getElementById('customVariantRows');
    const addCustomVariantBtn = document.getElementById('addCustomVariantBtn');
    let customVariantIndex = parseInt(customVariantRows?.dataset.initialCount || '0', 10);

    const customVariantProductOptions = `
        <option value="">Select Product</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
        @endforeach
    `;

    const customVariantOptionOptions = `
        <option value="">Select Option</option>
        @foreach($packingQuantityOptions as $option)
            <option value="{{ $option->id }}">{{ $option->name }}</option>
        @endforeach
    `;

    const toggleVariantInputs = (variantId, enabled) => {
        document.querySelectorAll(`.variant-field[data-variant-id="${variantId}"]`).forEach((field) => {
            field.disabled = !enabled;
        });
    };

    const bindVariantEvents = () => {
        document.querySelectorAll('.variant-select').forEach((checkbox) => {
            const variantId = checkbox.dataset.variantId;

            checkbox.addEventListener('change', () => {
                toggleVariantInputs(variantId, checkbox.checked);
            });

            toggleVariantInputs(variantId, checkbox.checked);
        });

        document.querySelectorAll('.variant-field').forEach((input) => {
            input.addEventListener('input', () => {
                const variantId = input.dataset.variantId;
                const checkbox = document.querySelector(`.variant-select[data-variant-id="${variantId}"]`);
                if (!checkbox) {
                    return;
                }

                if (!checkbox.checked && input.value !== '') {
                    checkbox.checked = true;
                    toggleVariantInputs(variantId, true);
                }
            });
        });
    };

    const setProductCardState = (card, visible) => {
        if (visible) {
            card.classList.remove('d-none');
            card.querySelectorAll('.variant-select').forEach((checkbox) => {
                toggleVariantInputs(checkbox.dataset.variantId, checkbox.checked);
            });
        } else {
            card.classList.add('d-none');
            card.querySelectorAll('.variant-select').forEach((checkbox) => {
                checkbox.checked = false;
            });
            card.querySelectorAll('.variant-field').forEach((field) => {
                field.value = '';
                field.disabled = true;
            });
        }
    };

    addProductToggleBtn?.addEventListener('click', () => {
        addProductPanel?.classList.toggle('d-none');
    });

    addSelectedProductBtn?.addEventListener('click', () => {
        const productId = productPicker?.value;
        if (!productId) {
            return;
        }

        const card = document.querySelector(`.product-add-card[data-product-id="${productId}"]`);
        if (!card) {
            return;
        }

        setProductCardState(card, true);

        const option = productPicker.querySelector(`option[value="${productId}"]`);
        if (option) {
            option.disabled = true;
        }

        productPicker.value = '';
        addProductPanel.classList.remove('d-none');
    });

    document.querySelectorAll('.remove-product-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            const card = document.querySelector(`.product-add-card[data-product-id="${productId}"]`);
            if (!card) {
                return;
            }

            setProductCardState(card, false);

            const option = productPicker?.querySelector(`option[value="${productId}"]`);
            if (option) {
                option.disabled = false;
            }
        });
    });

    document.querySelectorAll('.product-add-card').forEach((card) => {
        const isVisible = !card.classList.contains('d-none');
        setProductCardState(card, isVisible);
    });

    if (Array.from(document.querySelectorAll('.product-add-card')).some((card) => !card.classList.contains('d-none'))) {
        addProductPanel?.classList.remove('d-none');
    }

    addCustomVariantBtn?.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row g-2 custom-variant-row align-items-end border rounded p-2';
        row.innerHTML = `
            <div class="col-md-3">
                <label class="form-label small mb-1">Product</label>
                <select name="variants[new_custom_${customVariantIndex}][product_id]" class="form-select form-select-sm">
                    ${customVariantProductOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Variant Size</label>
                <input type="text" name="variants[new_custom_${customVariantIndex}][size]" class="form-control form-control-sm" placeholder="e.g. 12oz / Large">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Quantity</label>
                <input type="number" min="0" step="1" name="variants[new_custom_${customVariantIndex}][quantity]" class="form-control form-control-sm" placeholder="100">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Variant Option</label>
                <select name="variants[new_custom_${customVariantIndex}][packing_quantity_option_id]" class="form-select form-select-sm">
                    ${customVariantOptionOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Vendor Price (RM)</label>
                <input type="number" min="0" step="0.01" name="variants[new_custom_${customVariantIndex}][price]" class="form-control form-control-sm" placeholder="0.00">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-custom-variant-btn"><i class="fas fa-trash"></i></button>
            </div>
            <input type="hidden" name="variants[new_custom_${customVariantIndex}][selected]" value="1">
        `;
        customVariantRows?.appendChild(row);
        customVariantIndex++;
    });

    document.addEventListener('click', (event) => {
        const removeButton = event.target.closest('.remove-custom-variant-btn');
        if (!removeButton) {
            return;
        }

        removeButton.closest('.custom-variant-row')?.remove();
    });

    bindVariantEvents();
</script>
@endsection