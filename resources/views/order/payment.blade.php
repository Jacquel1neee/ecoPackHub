@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-credit-card fa-4x" style="color: var(--primary-green);"></i>
                    </div>
                    <h2 class="fw-bold mb-3" style="color: var(--primary-green);">Mock Payment</h2>
                    <p class="text-muted mb-4">
                        This is a local test payment flow for order #{{ $order->order_number }}.
                        Click below to simulate a successful payment.
                    </p>

                    <div class="alert alert-info text-start">
                        <strong>Order Summary</strong>
                        <ul class="mb-0 mt-2">
                            <li>Order Number: {{ $order->order_number }}</li>
                            <li>Total Amount: RM {{ number_format($order->total_amount, 2) }}</li>
                            <li>Payment Status: {{ $order->payment_status }}</li>
                        </ul>
                    </div>

                    <form action="{{ route('orders.pay.mock', ['order' => $order->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg px-4" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                            <i class="fas fa-check-circle me-2"></i> Pay Now
                        </button>
                    </form>

                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Back to Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
