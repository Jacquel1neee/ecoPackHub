@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-box me-2"></i>Products</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-green">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Variants</th>
                        <th>Price Range</th>
                        <th style="width: 130px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" class="product-img-thumb" alt="{{ $product->name }}">
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
                                <span class="badge bg-info">{{ $product->variants->count() }} variants</span>
                                @if($product->variants->count() > 0)
                                    <br>
                                    <small class="text-muted">
                                        @foreach($product->variants as $variant)
                                            {{ $variant->size ?? 'Standard' }}@if(!$loop->last), @endif
                                        @endforeach
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($product->variants->count() > 0)
                                    <span class="fw-bold" style="color: var(--primary-green);">
                                        RM {{ number_format($product->min_price, 2) }}
                                        @if($product->min_price != $product->max_price)
                                            - RM {{ number_format($product->max_price, 2) }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">No price</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1" style="flex-wrap: nowrap;">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary" style="padding: 2px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-warning" style="padding: 2px 8px; font-size: 12px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 8px; font-size: 12px;" onclick="return confirm('Delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x d-block mb-2"></i>
                                No products found. <a href="{{ route('admin.products.create') }}">Add your first product</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection