@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">All Products</li>
        </ol>
    </nav>

    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-box me-2"></i>All Products
        </h1>
        <span class="text-muted">{{ $allProducts->count() }} products</span>
    </div>

    <!-- ===== AI SUGGESTION SECTION ===== -->
    <div class="mb-5">
        <div class="card shadow-sm border-0 rounded-3" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-robot fa-3x" style="color: var(--primary-green);"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold mb-1" style="color: var(--primary-green);">
                            <i class="fas fa-lightbulb me-2"></i>AI Suggestion
                        </h5>
                        <p class="text-muted mb-2">Based on your browsing, we think you might like these products:</p>
                    </div>
                </div>
                <div class="row g-3 mt-2" id="ai-suggestions">
                    @php
                        $aiProducts = $allProducts->random(min(4, $allProducts->count()));
                    @endphp
                    @if($aiProducts->count() > 0)
                        @foreach($aiProducts as $product)
                            <div class="col-lg-3 col-md-4 col-6">
                                <div class="card product-card h-100 shadow-sm border-0 rounded-3">
                                    <a href="{{ route('product.show', $product) }}" class="text-decoration-none d-block position-relative" style="overflow: hidden; background: #f8f9fa; height: 150px;">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                        @endif
                                        <span class="position-absolute top-0 end-0 m-1 badge bg-primary bg-opacity-75 small">
                                            <i class="fas fa-robot me-1"></i> AI
                                        </span>
                                    </a>
                                    <div class="card-body p-2">
                                        <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                            <h6 class="card-title fw-semibold mb-0 text-truncate" style="font-size: 0.8rem;">{{ $product->name }}</h6>
                                        </a>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <span class="fw-bold" style="color: var(--primary-green); font-size: 0.85rem;">
                                                RM {{ number_format($product->min_price, 2) }}
                                            </span>
                                            <small class="text-muted" style="font-size: 0.6rem;">{{ $product->variants->count() }} variants</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center text-muted">
                            <p class="mb-0">No products available for suggestions yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PRODUCTS BY CATEGORY ===== -->
    @forelse($categories as $category)
        @if($category->products->count() > 0)
            <div class="mb-5" id="category-{{ $category->slug }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold" style="color: var(--primary-green);">
                        <i class="{{ $category->icon ?? 'fa-solid fa-box' }} me-2"></i>
                        {{ $category->name }}
                    </h4>
                    <span class="text-muted small">{{ $category->products->count() }} products</span>
                </div>
                <div class="row g-4">
                    @foreach($category->products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                            <div class="card product-card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                                <a href="{{ route('product.show', $product) }}" class="text-decoration-none d-block position-relative" style="overflow: hidden; background: #f8f9fa; height: 180px;">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: cover;">
                                    @else
                                        <img src="https://via.placeholder.com/300x200/2e7d32/ffffff?text={{ urlencode($product->code) }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: cover;">
                                    @endif
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 small px-2 py-1" style="font-size: 0.6rem;">
                                        {{ $product->code }}
                                    </span>
                                </a>
                                <div class="card-body d-flex flex-column p-3">
                                    <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                        <h6 class="card-title fw-semibold mb-1" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.4rem;">
                                            {{ $product->name }}
                                        </h6>
                                    </a>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold" style="color: var(--primary-green); font-size: 1rem;">
                                                RM {{ number_format($product->min_price, 2) }}
                                                @if($product->min_price != $product->max_price)
                                                    <small class="text-muted fw-normal" style="font-size: 0.7rem;">- RM {{ number_format($product->max_price, 2) }}</small>
                                                @endif
                                            </span>
                                            <small class="text-muted" style="font-size: 0.6rem;">{{ $product->variants->first()->packing_quantity ?? '' }}</small>
                                        </div>
                                        <a href="{{ route('product.show', $product) }}" class="btn btn-sm w-100 mt-2" 
                                           style="background-color: var(--primary-green); color: #fff; border-radius: 20px; border: none; text-decoration: none; display: inline-block; text-align: center; padding: 5px 0; font-size: 0.75rem;">
                                            <i class="fas fa-cart-plus me-1"></i> Select Options
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @empty
        <div class="text-center text-muted py-5">
            <i class="fas fa-box-open fa-3x d-block mb-3"></i>
            <p>No products available.</p>
        </div>
    @endforelse
</div>
@endsection