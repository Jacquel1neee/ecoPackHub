@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
        <i class="fas fa-credit-card me-2"></i>Checkout
    </h2>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-shipping-fast me-2"></i>Shipping Details
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.place') }}" method="POST">
                        @csrf
                        
                        <!-- Delivery Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Delivery Method *</label>
                            <div class="d-flex gap-3">
                                
                                <!-- Shipping 选项 -->
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
                                    </label>
                                    <div class="small text-muted mt-1">Deliver to your address</div>
                                </div>

                                <!-- Self Pickup 选项 -->
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
                                    </label>
                                    <div class="small text-muted mt-1">Pick up from our store</div>
                                </div>
                            </div>
                        </div>

                        <!-- 用户信息 -->
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
                        
                        <!-- Shipping Address -->
                        <div id="shipping-address-section">
                            <div class="mb-3">
                                <label class="form-label">Shipping Address *</label>
                                <textarea name="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" 
                                          placeholder="Your full address" required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Self Pickup Info -->
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
                            <input type="hidden" name="shipping_address" value="Self Pickup - EcoPack Hub Store">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn w-100" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 12px;">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-shopping-bag me-2"></i>Order Summary
                </div>
                <div class="card-body">
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
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span style="color: var(--primary-green); font-size: 1.3rem;">RM {{ number_format($total, 2) }}</span>
                    </div>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-3">
                        <i class="fas fa-arrow-left me-1"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * 选择配送方式并更新样式
 */
function selectDeliveryMethod(method) {
    // ===== 获取所有元素 =====
    const shippingOption = document.getElementById('shipping-option');
    const selfpickupOption = document.getElementById('selfpickup-option');
    const shippingRadio = document.getElementById('shipping');
    const selfpickupRadio = document.getElementById('selfpickup');
    const shippingSection = document.getElementById('shipping-address-section');
    const selfpickupSection = document.getElementById('selfpickup-section');
    const shippingAddressTextarea = document.querySelector('textarea[name="shipping_address"]');
    const hiddenAddressInput = document.querySelector('input[name="shipping_address"][type="hidden"]');
    const addressLabel = document.querySelector('#shipping-address-section .form-label');

    // ===== 移除所有选项的选中样式 =====
    shippingOption.classList.remove('selected');
    selfpickupOption.classList.remove('selected');

    if (method === 'shipping') {
        // ===== Shipping 模式 =====
        // 1. 选中 Shipping 按钮
        shippingRadio.checked = true;
        shippingOption.classList.add('selected'); // 添加绿色高亮
        
        // 2. 显示地址输入框
        shippingSection.style.display = 'block';
        selfpickupSection.style.display = 'none';
        
        // 3. 启用地址输入框
        shippingAddressTextarea.disabled = false;
        shippingAddressTextarea.required = true;
        shippingAddressTextarea.style.display = 'block';
        
        // 4. 隐藏隐藏输入
        hiddenAddressInput.style.display = 'none';
        hiddenAddressInput.disabled = true;
        
        // 5. 显示标签
        addressLabel.style.display = 'block';
        
    } else {
        // ===== Self Pickup 模式 =====
        // 1. 选中 Self Pickup 按钮
        selfpickupRadio.checked = true;
        selfpickupOption.classList.add('selected'); // 添加绿色高亮
        
        // 2. 显示自取信息
        shippingSection.style.display = 'none';
        selfpickupSection.style.display = 'block';
        
        // 3. 禁用地址输入框
        shippingAddressTextarea.disabled = true;
        shippingAddressTextarea.required = false;
        shippingAddressTextarea.style.display = 'none';
        
        // 4. 启用隐藏输入并填充地址
        hiddenAddressInput.style.display = 'block';
        hiddenAddressInput.disabled = false;
        hiddenAddressInput.value = 'Self Pickup - EcoPack Hub Store';
        
        // 5. 隐藏标签
        addressLabel.style.display = 'none';
    }
}

// ===== 页面加载时默认选中 Shipping =====
document.addEventListener('DOMContentLoaded', function() {
    // 默认选中 Shipping
    selectDeliveryMethod('shipping');
});
</script>

<style>
/* ===== 配送方式选项卡片样式 ===== */
.delivery-option {
    transition: all 0.3s ease; /* 平滑过渡效果 */
    border-width: 2px !important;
    border-color: #dee2e6 !important;
    background-color: #ffffff;
}

/* 悬停效果 */
.delivery-option:hover {
    background-color: #f8f9fa;
    border-color: #a8d5ba !important;
    transform: translateY(-2px); /* 轻微上浮 */
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* 选中效果 - 绿色高亮 */
.delivery-option.selected {
    background-color: #e8f5e9 !important;
    border-color: var(--primary-green, #2e7d32) !important;
    border-width: 2px !important;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
}

/* 选中后的图标颜色变化 */
.delivery-option.selected i {
    color: var(--primary-green, #2e7d32) !important;
}

/* 选中后的文字颜色 */
.delivery-option.selected .form-check-label {
    color: var(--primary-green, #2e7d32) !important;
}

/* 选中的单选按钮样式 */
.delivery-option.selected .form-check-input:checked {
    background-color: var(--primary-green, #2e7d32);
    border-color: var(--primary-green, #2e7d32);
}

/* 卡片点击区域 */
.delivery-option .form-check-label {
    width: 100%;
    cursor: pointer;
}

.delivery-option .form-check-input {
    cursor: pointer;
}

/* 禁用状态 */
.delivery-option:active {
    transform: scale(0.98);
}
</style>

@endsection