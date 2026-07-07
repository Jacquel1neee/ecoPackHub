@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
        <i class="fas fa-box me-2"></i>My Orders
    </h2>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ddd;"></i>
            <p class="mt-3 text-muted">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="btn" style="background-color: var(--primary-green); color: #fff;">
                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
            </a>
        </div>
    @else
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold">Order #{{ $order->order_number }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Delivery:</span>
                                <span>{{ $order->delivery_method_label }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted">Total:</span>
                                <span class="fw-bold" style="color: var(--primary-green);">
                                    RM {{ number_format($order->total_amount, 2) }}
                                </span>
                            </div>
                            
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary w-100 mt-3">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection