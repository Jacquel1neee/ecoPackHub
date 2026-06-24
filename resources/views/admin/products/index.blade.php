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
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
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
                                        <i class="fas fa-image fa-2x"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $product->code }}</strong></td>
                            <td>{{ $product->name }}</td>
                            <td><span class="badge bg-secondary">{{ $product->category->name ?? 'N/A' }}</span></td>
                            <td>RM {{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
@endsection