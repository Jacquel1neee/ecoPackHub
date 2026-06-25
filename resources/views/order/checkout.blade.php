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
                        <div class="mb-3">
                            <label class="form-label">Shipping Address *</label>
                            <textarea name="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" 
                                      placeholder="Your full address" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                            <span>RM {{ number_format($item->quantity * $item->product->price, 2) }}</span>
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
@endsection