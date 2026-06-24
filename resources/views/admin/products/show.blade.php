@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-eye me-2"></i>Product Details</h4>
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" style="max-width:100%;max-height:300px;border-radius:12px;">
                    @else
                        <div class="bg-light p-5 rounded">
                            <i class="fas fa-image fa-4x text-muted"></i>
                            <p class="text-muted mt-2">No image</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h3>{{ $product->name }}</h3>
                    <p><strong>Code:</strong> {{ $product->code }}</p>
                    <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                    <p><strong>Material:</strong> {{ $product->material ?? 'N/A' }}</p>
                    <p><strong>Size:</strong> {{ $product->size ?? 'N/A' }}</p>
                    <p><strong>Packing Quantity:</strong> {{ $product->packing_quantity }}</p>
                    <p><strong>Stock:</strong> {{ $product->stock ?? 'N/A' }}</p>
                    <p><strong>Price:</strong> RM {{ number_format($product->price, 2) }}</p>
                    <p><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-label">Total Sales</div>
                                <div class="stat-number">{{ $product->getTotalSalesAttribute() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-label">Total Revenue</div>
                                <div class="stat-number">RM {{ number_format($product->getTotalRevenueAttribute(), 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection