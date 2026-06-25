@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: var(--primary-green);">
        <i class="fas fa-shopping-cart me-2"></i>My Cart
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($items->count() > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image_path)
                                                        <img src="{{ asset($item->product->image_path) }}" 
                                                             style="width:50px;height:50px;object-fit:cover;border-radius:8px;margin-right:12px;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $item->product->code }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>RM {{ number_format($item->product->price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                                           min="1" max="999" style="width:60px;border-radius:8px;border:1px solid #ddd;padding:4px 8px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-success ms-2">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>RM {{ number_format($item->quantity * $item->product->price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Clear all items?')">
                            <i class="fas fa-trash-alt me-1"></i> Clear Cart
                        </button>
                    </form>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm ms-2">
                        <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green);">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items ({{ $items->count() }})</span>
                            <span>RM {{ number_format($total, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total</span>
                            <span style="color: var(--primary-green); font-size: 1.3rem;">RM {{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('checkout') }}" class="btn w-100" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 12px;">
                            <i class="fas fa-check me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <p class="lead">Your cart is empty</p>
            <a href="{{ route('home') }}" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                <i class="fas fa-arrow-left me-1"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection