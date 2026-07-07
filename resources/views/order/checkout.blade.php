@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
        <i class="fas fa-credit-card me-2"></i>Checkout
    </h2>

    <div class="row g-4">
        <!-- Left Column: Shipping Details Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-shipping-fast me-2"></i>Shipping Details
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.place') }}" method="POST" id="checkoutForm">
                        @csrf
                        
                        <!-- ===== Delivery Method Selection ===== -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Delivery Method *</label>
                            <div class="d-flex gap-3">
                                
                                <!-- Option 1: Shipping (with delivery fee) -->
                                <div class="form-check flex-fill p-3 border rounded-3 delivery-option" 
                                     id="shipping-option"
                                     style="cursor: pointer;" 
                                     onclick="selectDeliveryMethod('shipping')">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="delivery_method" 
                                           id="shipping" 
                                           value="shipping" 
                                           checked>
                                    <label class="form-check-label fw-bold" for="shipping" style="cursor: pointer;">
                                        <i class="fas fa-truck me-2" style="color: var(--primary-green);"></i>
                                        Shipping
                                        <span class="badge bg-warning ms-2">+ RM 5.00</span>
                                    </label>
                                    <div class="small text-muted mt-1">Deliver to your address (1-3 business days)</div>
                                </div>

                                <!-- Option 2: Self Pickup (FREE) -->
                                <div class="form-check flex-fill p-3 border rounded-3 delivery-option" 
                                     id="selfpickup-option"
                                     style="cursor: pointer;" 
                                     onclick="selectDeliveryMethod('selfpickup')">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="delivery_method" 
                                           id="selfpickup" 
                                           value="selfpickup">
                                    <label class="form-check-label fw-bold" for="selfpickup" style="cursor: pointer;">
                                        <i class="fas fa-store me-2" style="color: var(--primary-green);"></i>
                                        Self Pickup
                                        <span class="badge bg-success ms-2">FREE</span>
                                    </label>
                                    <div class="small text-muted mt-1">Pick up from our store</div>
                                </div>
                            </div>
                        </div>

                        <!-- ===== User Information (Read-only) ===== -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="e.g., 012-3456789" required value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <!-- ===== Shipping Address (visible only for shipping) ===== -->
                        <div id="shipping-address-section">
                            <div class="mb-3">
                                <label class="form-label">Shipping Address *</label>
                                <textarea name="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" 
                                          placeholder="Your full address" required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- ===== Self Pickup Info (visible only for self pickup) ===== -->
                        <div id="selfpickup-section" style="display: none;">
                            <div class="alert alert-success" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
                                <i class="fas fa-store me-2"></i>
                                <strong>Self Pickup Location:</strong><br>
                                <p class="mt-2 mb-0">
                                    📍 EcoPack Hub Store<br>
                                    123, Jalan Example,<br>
                                    43000 Kajang, Selangor<br>
                                    <br>
                                    ⏰ Operating Hours: 10:00 AM - 8:00 PM (Daily)
                                </p>
                            </div>
                            <!-- Hidden input: auto-fill address for self pickup -->
                            <input type="hidden" name="shipping_address" value="Self Pickup - EcoPack Hub Store">
                        </div>

                        <!-- ===== Order Notes (Optional) ===== -->
                        <div class="mb-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                        </div>
                        
                        <!-- ===== Submit Button ===== -->
                        <button type="submit" class="btn w-100" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 12px;">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ===== Right Column: Order Summary ===== -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-shopping-bag me-2"></i>Order Summary
                </div>
                <div class="card-body">
                    <!-- Loop through cart items -->
                    @foreach($items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                {{ $item->variant->product->name ?? 'Product' }}
                                @if($item->variant->size)
                                    ({{ $item->variant->size }})
                                @endif
                                × {{ $item->quantity }}
                            </span>
                            <span>RM {{ number_format($item->quantity * ($item->variant->price ?? 0), 2) }}</span>
                        </div>
                    @endforeach
                    
                    <hr>
                    
                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>RM {{ number_format($total, 2) }}</span>
                    </div>
                    
                    <!-- Shipping Fee (dynamically updated) -->
                    <div class="d-flex justify-content-between mt-2" id="shipping-fee-row">
                        <span>Shipping Fee</span>
                        <span id="shipping-fee-amount">RM 5.00</span>
                    </div>
                    
                    <hr>
                    
                    <!-- Grand Total -->
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span style="color: var(--primary-green); font-size: 1.3rem;" id="total-amount">
                            RM {{ number_format($total + 5.00, 2) }}
                        </span>
                    </div>
                    
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-3">
                        <i class="fas fa-arrow-left me-1"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== JavaScript: Toggle delivery method and update prices ===== -->
<script>
// Shipping fee constant
const SHIPPING_FEE = 5.00;
const SUBTOTAL = {{ $total }};

/**
 * Select delivery method and update UI
 * @param {string} method - 'shipping' or 'selfpickup'
 */
function selectDeliveryMethod(method) {
    // Get DOM elements
    const shippingOption = document.getElementById('shipping-option');
    const selfpickupOption = document.getElementById('selfpickup-option');
    const shippingRadio = document.getElementById('shipping');
    const selfpickupRadio = document.getElementById('selfpickup');
    const shippingSection = document.getElementById('shipping-address-section');
    const selfpickupSection = document.getElementById('selfpickup-section');
    const shippingAddressTextarea = document.querySelector('textarea[name="shipping_address"]');
    const hiddenAddressInput = document.querySelector('input[name="shipping_address"][type="hidden"]');
    const addressLabel = document.querySelector('#shipping-address-section .form-label');
    
    // Price display elements
    const shippingFeeAmount = document.getElementById('shipping-fee-amount');
    const totalAmount = document.getElementById('total-amount');

    // Remove selected styles from all options
    shippingOption.classList.remove('selected');
    selfpickupOption.classList.remove('selected');

    if (method === 'shipping') {
        // ===== SHIPPING MODE =====
        shippingRadio.checked = true;
        shippingOption.classList.add('selected');
        
        // Show shipping address, hide self pickup
        shippingSection.style.display = 'block';
        selfpickupSection.style.display = 'none';
        
        // Enable shipping address textarea
        shippingAddressTextarea.disabled = false;
        shippingAddressTextarea.required = true;
        shippingAddressTextarea.style.display = 'block';
        
        // Hide hidden input
        hiddenAddressInput.style.display = 'none';
        hiddenAddressInput.disabled = true;
        
        // Show address label
        addressLabel.style.display = 'block';
        
        // ===== Update prices (add shipping fee) =====
        const totalWithShipping = SUBTOTAL + SHIPPING_FEE;
        shippingFeeAmount.textContent = 'RM ' + SHIPPING_FEE.toFixed(2);
        totalAmount.textContent = 'RM ' + totalWithShipping.toFixed(2);
        
    } else {
        // ===== SELF PICKUP MODE =====
        selfpickupRadio.checked = true;
        selfpickupOption.classList.add('selected');
        
        // Show self pickup, hide shipping address
        shippingSection.style.display = 'none';
        selfpickupSection.style.display = 'block';
        
        // Disable shipping address textarea
        shippingAddressTextarea.disabled = true;
        shippingAddressTextarea.required = false;
        shippingAddressTextarea.style.display = 'none';
        
        // Show hidden input with auto-filled address
        hiddenAddressInput.style.display = 'block';
        hiddenAddressInput.disabled = false;
        hiddenAddressInput.value = 'Self Pickup - EcoPack Hub Store';
        
        // Hide address label
        addressLabel.style.display = 'none';
        
        // ===== Update prices (FREE shipping) =====
        shippingFeeAmount.textContent = 'FREE';
        totalAmount.textContent = 'RM ' + SUBTOTAL.toFixed(2);
    }
}

// ===== Initialize on page load =====
document.addEventListener('DOMContentLoaded', function() {
    selectDeliveryMethod('shipping');
});

// ===== Hover effects for delivery options =====
document.querySelectorAll('.delivery-option').forEach(option => {
    option.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    });
    option.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});
</script>

<style>
/* Delivery option card styles */
.delivery-option {
    transition: all 0.3s ease;
    border-width: 2px !important;
    border-color: #dee2e6 !important;
    background-color: #ffffff;
}

/* Selected state */
.delivery-option.selected {
    background-color: #e8f5e9 !important;
    border-color: var(--primary-green, #2e7d32) !important;
    border-width: 2px !important;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
}

/* Selected icon color */
.delivery-option.selected i {
    color: var(--primary-green, #2e7d32) !important;
}

/* Selected label color */
.delivery-option.selected .form-check-label {
    color: var(--primary-green, #2e7d32) !important;
}

/* Selected radio button */
.delivery-option.selected .form-check-input:checked {
    background-color: var(--primary-green, #2e7d32);
    border-color: var(--primary-green, #2e7d32);
}
</style>
@endsection