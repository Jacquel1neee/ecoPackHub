@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-plus-circle me-2"></i>Add Vendor
        </h2>
        <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.vendors.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Vendor Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                   value="{{ old('contact_person') }}">
                            @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                                   id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                @php
                    $oldVariantRows = old('variants', []);
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
                        <h5 class="mb-1 fw-bold" style="color: var(--primary-green);">Assign Product Variants</h5>
                        <p class="text-muted small mb-0">Click <strong>Add Product</strong>, choose a product, then select the variants this vendor supplies.</p>
                    </div>
                    <button type="button" class="btn btn-outline-success" id="addProductToggleBtn">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>

                <div id="addProductPanel" class="card border-success mb-4 {{ $preloadedProductIds->isNotEmpty() ? '' : 'd-none' }}">
                    <div class="card-body">
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-8">
                                <label for="productPicker" class="form-label fw-semibold mb-1">Choose Product to Add</label>
                                <select id="productPicker" class="form-select">
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        @if($product->variants->count() > 0)
                                            <option value="{{ $product->id }}" {{ $preloadedProductIds->contains((int) $product->id) ? 'disabled' : '' }}>
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
                                @if($product->variants->isEmpty())
                                    @continue
                                @endif

                                @php
                                    $showCardByDefault = $preloadedProductIds->contains((int) $product->id);
                                @endphp

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
                                                    @foreach($product->variants as $variant)
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

                <button type="submit" class="btn" style="background-color: var(--primary-green); color: #fff;">
                    <i class="fas fa-save me-2"></i>Save Vendor
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

    bindVariantEvents();
</script>
@endsection