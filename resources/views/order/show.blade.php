@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-receipt me-2"></i>Order #{{ $order->order_number }}
        </h2>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-box me-2"></i>Order Items
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->variant && $item->variant->product && $item->variant->product->image_url)
                                                <img src="{{ $item->variant->product->image_url }}"
                                                     style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:10px;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->variant->product->name ?? 'Product' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $item->variant->product->code ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $item->variant->size ?? 'Standard' }}
                                            <br>
                                            {{ $item->variant->packing_quantity ?? '' }}
                                        </small>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>RM {{ number_format($item->price, 2) }}</td>
                                    <td>RM {{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total</td>
                                <td style="color: var(--primary-green);">RM {{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="card shadow-sm border-0 rounded-3 mt-3">
                    <div class="card-body">
                        <strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong>
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Order Details
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> <span class="badge bg-{{ $order->statusColor }}">{{ $order->statusLabel }}</span></p>
                    <p><strong>Payment:</strong> <span class="badge bg-{{ $order->paymentStatusColor }}">{{ $order->paymentStatusLabel }}</span></p>
                    <p><strong>Order Date:</strong><br>{{ $order->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Shipping Address:</strong><br>{{ $order->shipping_address }}</p>
                    <p><strong>Phone:</strong><br>{{ $order->phone }}</p>
                    @if($order->payment_status == 'pending')
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-clock me-2"></i> Your order is waiting for payment.
                        </div>
                        <a href="{{ $order->id ? url('/orders/' . $order->id . '/pay') : url('/orders/1/pay') }}"
                           class="btn w-100 mt-2"
                           style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                            <i class="fas fa-credit-card me-2"></i> Pay Now
                        </a>
                    @elseif($order->status == 'completed')
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-check-circle me-2"></i> Order completed!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection