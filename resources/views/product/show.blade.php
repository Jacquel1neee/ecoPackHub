@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home') }}#products">{{ $product->category->name ?? 'Products' }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-3 text-center">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 400px; object-fit: contain; border-radius: 12px;">
                    @else
                        <img src="https://via.placeholder.com/500x400/2e7d32/ffffff?text={{ urlencode($product->code) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 400px; object-fit: contain; border-radius: 12px;">
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <span class="badge bg-secondary mb-2">{{ $product->code }}</span>
                    <h2 class="fw-bold mb-2">{{ $product->name }}</h2>
                    
                    <div class="mb-3">
                        <span class="text-muted">Material: </span>
                        <span class="badge bg-light text-dark">{{ $product->material ?? 'N/A' }}</span>
                    </div>

                    <p class="text-muted">{{ $product->description }}</p>

                    <!-- Price Range -->
                    <div class="mb-3">
                        <span class="text-muted">Price Range: </span>
                        <span class="fw-bold" style="color: var(--primary-green); font-size: 1.3rem;">
                            RM {{ number_format($product->min_price, 2) }}
                            @if($product->min_price != $product->max_price)
                                - RM {{ number_format($product->max_price, 2) }}
                            @endif
                        </span>
                        <br>
                        <small class="text-muted">(Starting from {{ $product->variants->count() }} variants)</small>
                    </div>

                    <!-- Variant Selection -->
                    <form id="add-to-cart-form" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Size/Variant</label>
                            <select name="variant_id" id="variant-select" class="form-select" required style="border-radius: 12px; border: 2px solid #e0e0e0;">
                                <option value="">-- Please select --</option>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" 
                                            data-price="{{ $variant->price }}"
                                            data-stock="{{ $variant->stock }}"
                                            data-packing="{{ $variant->packing_quantity }}">
                                        {{ $variant->size ?? 'Standard' }} - {{ $variant->packing_quantity }} 
                                        @if($variant->stock > 0)
                                            <span class="text-success">(In Stock: {{ $variant->stock }})</span>
                                        @else
                                            <span class="text-danger">(Out of Stock)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity</label>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)" style="border-radius: 20px 0 0 20px;">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity-input" value="1" min="1" max="999" 
                                       class="form-control text-center" style="width: 80px; border-radius: 0;">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)" style="border-radius: 0 20px 20px 0;">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <span class="ms-3 text-muted" id="stock-display">Stock: 0</span>
                            </div>
                        </div>

                        <div class="mb-3" id="price-display">
                            <span class="fw-bold" style="color: var(--primary-green); font-size: 1.5rem;">
                                RM <span id="selected-price">0.00</span>
                            </span>
                            <span class="text-muted ms-2" id="packing-display"></span>
                        </div>

                        <button type="submit" class="btn w-100 mt-2" id="add-to-cart-btn" 
                                style="background-color: var(--primary-green); color: #fff; border-radius: 30px; padding: 14px; font-size: 1.1rem;">
                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                        </button>
                    </form>

                    <!-- Back to Products -->
                    <a href="{{ route('home') }}#products" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 30px;">
                        <i class="fas fa-arrow-left me-2"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Specifications -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green);">
                    <i class="fas fa-list me-2"></i> Product Specifications
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Packing Quantity</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td><strong>{{ $variant->size ?? 'Standard' }}</strong></td>
                                        <td>{{ $variant->packing_quantity }}</td>
                                        <td style="color: var(--primary-green); font-weight: bold;">RM {{ number_format($variant->price, 2) }}</td>
                                        <td>
                                            @if($variant->stock > 0)
                                                <span class="badge bg-success">{{ $variant->stock }} in stock</span>
                                            @else
                                                <span class="badge bg-danger">Out of stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== JAVASCRIPT ===== -->
<script>
    const variantSelect = document.getElementById('variant-select');
    const priceDisplay = document.getElementById('selected-price');
    const packingDisplay = document.getElementById('packing-display');
    const stockDisplay = document.getElementById('stock-display');
    const quantityInput = document.getElementById('quantity-input');
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    // Update display when variant changes
    variantSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            const price = parseFloat(selectedOption.dataset.price);
            const stock = parseInt(selectedOption.dataset.stock);
            const packing = selectedOption.dataset.packing;
            
            priceDisplay.textContent = price.toFixed(2);
            packingDisplay.textContent = '(' + packing + ')';
            stockDisplay.textContent = 'Stock: ' + stock;
            
            // Check stock
            if (stock <= 0) {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i> Out of Stock';
                addToCartBtn.style.backgroundColor = '#dc3545';
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i> Add to Cart';
                addToCartBtn.style.backgroundColor = 'var(--primary-green)';
            }
            
            // Reset quantity
            quantityInput.value = 1;
        } else {
            priceDisplay.textContent = '0.00';
            packingDisplay.textContent = '';
            stockDisplay.textContent = 'Stock: 0';
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please select variant';
            addToCartBtn.style.backgroundColor = '#6c757d';
        }
    });

    // Change quantity
    function changeQuantity(delta) {
        let val = parseInt(quantityInput.value) || 1;
        val = Math.max(1, Math.min(999, val + delta));
        quantityInput.value = val;
    }

    // AJAX Add to Cart
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const variantId = variantSelect.value;
        if (!variantId) {
            alert('Please select a variant first!');
            return;
        }

        const quantity = quantityInput.value;
        const formData = new FormData();
        formData.append('variant_id', variantId);
        formData.append('quantity', quantity);

        // Show loading state
        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                showToast('success', data.message || 'Added to cart!');
                // Update cart badge
                updateCartBadge(data.cart_count);
            } else {
                showToast('error', data.message || 'Failed to add to cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Something went wrong. Please try again.');
        })
        .finally(() => {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i> Add to Cart';
            addToCartBtn.style.backgroundColor = 'var(--primary-green)';
        });
    });

    // Show toast notification
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header ${type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'}">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                    <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast.parentNode) toast.remove();
        }, 4000);
    }

    // Update cart badge
    function updateCartBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.textContent = count;
        }
    }
</script>
@endsection