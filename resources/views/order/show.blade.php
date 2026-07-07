@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Order Header --}}
            <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
                <i class="fas fa-receipt me-2"></i>Order Details
            </h2>
            
            {{-- Order Info Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Order #{{ $order->order_number }}</span>
                    <span class="badge bg-{{ $order->status_color }} fs-6">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                            <p><strong>Phone:</strong> {{ $order->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Delivery Method:</strong> 
                                <i class="fas {{ $order->delivery_method_icon }} me-1"></i>
                                {{ $order->delivery_method_label }}
                            </p>
                            <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
                        </div>
                    </div>
                    
                    @if($order->notes)
                        <hr>
                        <p><strong>Notes:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>

            {{-- Order Items --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold"><i class="fas fa-box me-2"></i>Items</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->variant->product->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($item->variant->size)
                                                {{ $item->variant->size }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">RM {{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">RM {{ number_format($item->quantity * $item->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end">RM {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Shipping Fee:</td>
                                    <td class="text-end">
                                        @if($order->shipping_fee > 0)
                                            RM {{ number_format($order->shipping_fee, 2) }}
                                        @else
                                            <span class="text-success">FREE</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="fw-bold" style="color: var(--primary-green);">
                                    <td colspan="4" class="text-end fs-5">Total:</td>
                                    <td class="text-end fs-5">RM {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-cog me-2"></i>Quick Actions</h6>
                    <hr>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                    <a href="{{ route('products.index') }}" class="btn w-100" style="background-color: var(--primary-green); color: #fff;">
                        <i class="fas fa-shopping-cart me-2"></i>Shop More
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection