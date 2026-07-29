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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1 fw-bold" style="color: var(--primary-green);">Assigned Product Variants</h5>
                        <p class="text-muted small mb-0">Select each variant this vendor supplies and set quantity, variant option, and vendor price per variant.</p>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Use</th>
                                <th>Product</th>
                                <th style="width: 140px;">Variant Size</th>
                                <th>Current Vendors</th>
                                <th style="width: 160px;">Quantity</th>
                                <th style="width: 180px;">Variant Option</th>
                                <th style="width: 180px;">Vendor Price (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $variantCount = $product->variants->count();
                                    $productHasSelection = $product->variants->contains(function ($variant) {
                                        return old('variants.' . $variant->id . '.selected');
                                    });
                                @endphp

                                @if($variantCount === 0)
                                    <tr>
                                        <td></td>
                                        <td>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">{{ $product->code }}</small>
                                        </td>
                                        <td colspan="5">
                                            <span class="text-muted small">No variants found for this product.</span>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($product->variants as $variantIndex => $variant)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="variants[{{ $variant->id }}][selected]" value="1" class="form-check-input variant-select" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}" {{ old('variants.' . $variant->id . '.selected') ? 'checked' : '' }}>
                                            </td>

                                            @if($variantIndex === 0)
                                                <td rowspan="{{ $variantCount }}" class="align-top">
                                                    <strong>{{ $product->name }}</strong><br>
                                                    <small class="text-muted">{{ $product->code }}</small>
                                                    <div class="form-check mt-2">
                                                        <input type="checkbox" class="form-check-input product-select" data-product-id="{{ $product->id }}" id="product-select-{{ $product->id }}" {{ $productHasSelection ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="product-select-{{ $product->id }}">Supply this product</label>
                                                    </div>
                                                </td>
                                            @endif

                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $variant->size ?: 'Default' }}</span>
                                            </td>

                                            @if($variantIndex === 0)
                                                <td rowspan="{{ $variantCount }}" class="align-top">
                                                    @forelse($product->vendors as $assignedVendor)
                                                        <span class="badge bg-secondary me-1 mb-1">{{ $assignedVendor->name }}</span>
                                                    @empty
                                                        <span class="text-muted small">Not assigned yet</span>
                                                    @endforelse
                                                </td>
                                            @endif

                                            <td>
                                                <input type="number" step="1" min="0" name="variants[{{ $variant->id }}][quantity]" class="form-control form-control-sm variant-quantity" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}" value="{{ old('variants.' . $variant->id . '.quantity') }}" placeholder="100">
                                            </td>
                                            <td>
                                                <select name="variants[{{ $variant->id }}][packing_quantity_option_id]" class="form-select form-select-sm variant-option" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                                                    <option value="">Select Variant Option</option>
                                                    @foreach($packingQuantityOptions as $option)
                                                        <option value="{{ $option->id }}" {{ old('variants.' . $variant->id . '.packing_quantity_option_id') == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="variants[{{ $variant->id }}][price]" class="form-control form-control-sm variant-price" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}" value="{{ old('variants.' . $variant->id . '.price') }}" placeholder="0.00">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn" style="background-color: var(--primary-green); color: #fff;">
                    <i class="fas fa-save me-2"></i>Save Vendor
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const variantCheckboxes = Array.from(document.querySelectorAll('.variant-select'));
    const productCheckboxes = Array.from(document.querySelectorAll('.product-select'));

    const toggleVariantInputs = (variantId, enabled) => {
        const quantityInput = document.querySelector(`.variant-quantity[data-variant-id="${variantId}"]`);
        const optionSelect = document.querySelector(`.variant-option[data-variant-id="${variantId}"]`);
        const priceInput = document.querySelector(`.variant-price[data-variant-id="${variantId}"]`);

        if (quantityInput) quantityInput.disabled = !enabled;
        if (optionSelect) optionSelect.disabled = !enabled;
        if (priceInput) priceInput.disabled = !enabled;
    };

    const updateProductState = (productId) => {
        const productCheckbox = document.querySelector(`.product-select[data-product-id="${productId}"]`);
        if (!productCheckbox) {
            return;
        }

        const scopedVariants = variantCheckboxes.filter((cb) => cb.dataset.productId === String(productId));
        const checkedCount = scopedVariants.filter((cb) => cb.checked).length;

        productCheckbox.checked = checkedCount > 0;
        productCheckbox.indeterminate = checkedCount > 0 && checkedCount < scopedVariants.length;
    };

    productCheckboxes.forEach((productCheckbox) => {
        productCheckbox.addEventListener('change', () => {
            const productId = productCheckbox.dataset.productId;
            const scopedVariants = variantCheckboxes.filter((cb) => cb.dataset.productId === String(productId));

            scopedVariants.forEach((variantCheckbox) => {
                variantCheckbox.checked = productCheckbox.checked;
                toggleVariantInputs(variantCheckbox.dataset.variantId, productCheckbox.checked);
            });

            productCheckbox.indeterminate = false;
        });
    });

    variantCheckboxes.forEach((checkbox) => {
        const variantId = checkbox.dataset.variantId;
        const productId = checkbox.dataset.productId;

        checkbox.addEventListener('change', () => {
            toggleVariantInputs(variantId, checkbox.checked);
            updateProductState(productId);
        });

        toggleVariantInputs(variantId, checkbox.checked);
    });

    document.querySelectorAll('.variant-quantity, .variant-option, .variant-price').forEach((input) => {
        const variantId = input.dataset.variantId;
        const productId = input.dataset.productId;

        input.addEventListener('input', () => {
            const checkbox = document.querySelector(`.variant-select[data-variant-id="${variantId}"]`);
            if (!checkbox) {
                return;
            }

            if (!checkbox.checked && input.value !== '') {
                checkbox.checked = true;
                toggleVariantInputs(variantId, true);
                updateProductState(productId);
            }
        });

        input.addEventListener('change', () => {
            const checkbox = document.querySelector(`.variant-select[data-variant-id="${variantId}"]`);
            if (!checkbox) {
                return;
            }

            if (!checkbox.checked && input.value !== '') {
                checkbox.checked = true;
                toggleVariantInputs(variantId, true);
                updateProductState(productId);
            }
        });
    });

    productCheckboxes.forEach((productCheckbox) => {
        updateProductState(productCheckbox.dataset.productId);
    });
</script>
@endsection