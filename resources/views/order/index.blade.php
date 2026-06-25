@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
        <i class="fas fa-list me-2"></i>My Orders
    </h2>

    @if($orders->count() > 0)
        <div class="row g-3">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>#{{ $order->order_number }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-{{ $order->statusColor }} fs-6">
                                        {{ $order->statusLabel }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>RM {{ number_format($order->total_amount, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->items->count() }} items</small>
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <p class="lead">No orders yet</p>
            <a href="{{ route('home') }}" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                <i class="fas fa-shopping-bag me-1"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection