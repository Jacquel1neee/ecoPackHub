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
                <p><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
                
                <hr>
                <h5 class="fw-bold" style="color: var(--primary-green);">
                    <i class="fas fa-layer-group me-2"></i>Variants
                </h5>
                
                @if($product->variants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Packing Quantity</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td><strong>{{ $variant->size ?? 'Standard' }}</strong></td>
                                        <td>{{ $variant->packing_quantity }}</td>
                                        <td style="color: var(--primary-green); font-weight: bold;">
                                            RM {{ number_format($variant->price, 2) }}
                                        </td>
                                        <td>
                                            @if($variant->stock > 0)
                                                <span class="badge bg-success">{{ $variant->stock }} in stock</span>
                                            @else
                                                <span class="badge bg-danger">Out of stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end">Price Range:</td>
                                    <td colspan="2" style="color: var(--primary-green);">
                                        RM {{ number_format($product->min_price, 2) }}
                                        @if($product->min_price != $product->max_price)
                                            - RM {{ number_format($product->max_price, 2) }}
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No variants for this product.</p>
                @endif

                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Sales</div>
                            <div class="stat-number">{{ $product->total_sales }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Revenue</div>
                            <div class="stat-number">RM {{ number_format($product->total_revenue, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection