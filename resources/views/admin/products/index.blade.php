@extends('admin.layouts.app')

@section('content')
<style>
    .admin-products-pagination .pagination {
        margin-bottom: 0;
    }

    .admin-products-pagination .page-link {
        padding: 0.25rem 0.55rem;
        font-size: 0.875rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-box me-2"></i>Products</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-green">
        <i class="fas fa-plus me-2"></i>Add Product
    </a>
</div>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Discount</th>
                        <th>Vendors & Prices</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="product-img-thumb" alt="{{ $product->name }}">
                                @else
                                    <div class="product-img-thumb bg-light d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $product->code }}</strong></td>
                            <td>{{ $product->name }}</td>
                            <td><span class="badge bg-secondary">{{ $product->category->name ?? 'N/A' }}</span></td>
                            <td>
                                @if($product->has_active_discount)
                                    <span class="badge bg-success mb-1">Active</span><br>
                                    @if($product->discount_price !== null)
                                        <small>RM {{ number_format((float) $product->discount_price, 2) }}</small>
                                    @elseif($product->discount_percentage !== null)
                                        <small>{{ number_format((float) $product->discount_percentage, 2) }}%</small>
                                    @endif
                                @elseif($product->discount_price !== null || $product->discount_percentage !== null)
                                    <span class="badge bg-warning text-dark mb-1">Configured</span><br>
                                    @if($product->discount_price !== null)
                                        <small>RM {{ number_format((float) $product->discount_price, 2) }}</small>
                                    @elseif($product->discount_percentage !== null)
                                        <small>{{ number_format((float) $product->discount_percentage, 2) }}%</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No Discount</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $vendorGroups = $product->variants->filter(fn ($variant) => $variant->vendor)->groupBy('vendor_id');
                                @endphp
                                @forelse($vendorGroups as $vendorVariants)
                                    @php $vendor = $vendorVariants->first()->vendor; @endphp
                                    <div class="mb-1">
                                        <div><span class="badge bg-info">{{ $vendor->name }}</span></div>
                                        <div class="small">Sell Price: RM {{ $vendorVariants->pluck('price')->map(fn ($price) => number_format($price, 2))->implode(', RM ') }}</div>
                                        <div class="small text-muted">Cost Price: RM {{ $vendorVariants->pluck('vendor_price')->map(fn ($price) => number_format($price, 2))->implode(', RM ') }}</div>
                                    </div>
                                @empty
                                    <span class="text-muted">No vendors assigned</span>
                                @endforelse
                            </td>
                            <td>
                                @php
                                    $totalStock = $product->variants->sum('stock');
                                @endphp
                                @if($totalStock > 0)
                                    <span class="badge bg-success">{{ $totalStock }}</span>
                                @else
                                    <span class="badge bg-danger">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x d-block mb-2"></i>
                                No products found.
                                <a href="{{ route('admin.products.create') }}">Add your first product</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-3 admin-products-pagination">
                {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection