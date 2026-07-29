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
        <div class="row g-4">
            <div class="col-md-4 text-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:300px;border-radius:12px;">
                @else
                    <div class="bg-light p-5 rounded">
                        <i class="fas fa-image fa-4x text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No image</p>
                    </div>
                @endif
            </div>

            <div class="col-md-8">
                <h3 class="fw-bold mb-3">{{ $product->name }}</h3>
                <p><strong>Code:</strong> {{ $product->code }}</p>
                <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                <p><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>

                <p class="mb-2"><strong>Available Vendors:</strong></p>
                <div class="mb-3">
                    @forelse($product->vendors as $vendor)
                        <span class="badge bg-info me-1 mb-1">
                            {{ $vendor->name }} — Qty {{ (int) ($vendor->pivot->quantity ?? 0) }}
                            @if(!empty($vendor->pivot->packing_quantity_option_id))
                                ({{ $packingQuantityOptions->get($vendor->pivot->packing_quantity_option_id)?->name ?? 'Option' }})
                            @endif
                            — Vendor RM {{ number_format((float) $vendor->pivot->price, 2) }}
                        </span>
                    @empty
                        <span class="text-muted">No vendors assigned yet.</span>
                    @endforelse
                </div>

                <hr>
                <h5 class="fw-bold" style="color: var(--primary-green);">
                    <i class="fas fa-layer-group me-2"></i>Variants
                </h5>

                @if($product->variants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>Vendor Quantity</th>
                                    <th>Vendor</th>
                                    <th>Vendor Price</th>
                                    <th>Sell Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    @php
                                        $vendorAssignment = $product->vendors->firstWhere('id', $variant->vendor_id);
                                        $vendorOption = $variant->packing_quantity_option_id
                                            ? $packingQuantityOptions->get($variant->packing_quantity_option_id)?->name
                                            : $variant->packingQuantityDisplay;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $variant->size ?? 'Standard' }}</strong>
                                            @if($vendorOption)
                                                <div class="text-muted small">{{ $vendorOption }}</div>
                                            @endif
                                        </td>
                                        <td>{{ (int) ($variant->vendor_quantity ?? $vendorAssignment?->pivot?->quantity ?? 0) }}</td>
                                        <td>{{ $variant->vendor->name ?? 'N/A' }}</td>
                                        <td>RM {{ number_format((float) ($variant->vendor_price ?? $vendorAssignment?->pivot?->price ?? 0), 2) }}</td>
                                        <td style="color: var(--primary-green); font-weight: bold;">RM {{ number_format((float) $variant->price, 2) }}</td>
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
                                    <td colspan="3" class="text-end">Price Range:</td>
                                    <td colspan="3" style="color: var(--primary-green);">
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
                <div class="row g-3">
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