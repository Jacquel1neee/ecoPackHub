@extends('layouts.app')

@section('content')
    <!-- ===== HERO SECTION ===== -->
    <section class="hero">
        <div class="container text-center">
            <h1 class="mb-3">Sustainable Packaging Solutions</h1>
            <p class="lead mb-4">Biodegradable &amp; paper-based packaging for food businesses</p>
            <a href="#products" class="btn btn-primary-custom">
                <i class="fas fa-box-open me-2"></i>Browse Products
            </a>
        </div>
    </section>

    <div class="container">
        <!-- ===== CATEGORIES SECTION ===== -->
        <section id="categories" class="mb-5">
            <h2 class="text-center mb-4" style="color: var(--primary-green);">
                <i class="fas fa-th-large me-2"></i>Product Categories
            </h2>
            <div class="row g-4">
                @forelse($categories as $category)
                    <div class="col-6 col-md-3">
                        <a href="#" class="category-card">
                            <i class="{{ $category->icon ?? 'fa-solid fa-box' }}"></i>
                            <h5>{{ $category->name }}</h5>
                            <small class="text-muted">{{ $category->products_count ?? 0 }} products</small>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        <p>No categories available.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- ===== PRODUCTS SECTION ===== -->
        <section id="products" class="mb-5">
            <h2 class="text-center mb-4" style="color: var(--primary-green);">
                <i class="fas fa-box me-2"></i>Our Products
            </h2>
            
            @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-md-4 col-lg-3">
                            <div class="product-card">
                                <!-- Product Image -->
                                @if($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">
                                @else
                                    <img src="https://via.placeholder.com/300x200/2e7d32/ffffff?text={{ urlencode($product->code) }}" alt="{{ $product->name }}">
                                @endif
                                
                                <div class="card-body">
                                    <!-- Product Code Badge -->
                                    <span class="badge-code">{{ $product->code }}</span>
                                    
                                    <!-- Product Name -->
                                    <h5 class="card-title mt-1">{{ $product->name }}</h5>
                                    
                                    <!-- Product Description (shortened) -->
                                    <p class="card-text">{{ Str::limit($product->description, 60) }}</p>
                                    
                                    <!-- Price & Enquire Button -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="price">RM {{ number_format($product->price, 2) }}</span>
                                            <small class="text-muted d-block">{{ $product->packing_quantity }}</small>
                                        </div>
                                        <a href="#" class="btn btn-enquire">
                                            <i class="fas fa-envelope"></i> Enquire
                                        </a>
                                    </div>
                                    
                                    <!-- Material & Size -->
                                    <div class="mt-2">
                                        @if($product->material)
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i> {{ $product->material }}
                                            </small>
                                        @endif
                                        @if($product->size)
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-ruler"></i> {{ $product->size }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-box-open fa-3x d-block mb-3"></i>
                    <p>No products available yet. Please check back later.</p>
                </div>
            @endif
        </section>

        <!-- ===== ADVANTAGES SECTION ===== -->
        <section class="mb-5">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-recycle fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Sustainable Materials</h5>
                        <p>All products are made from biodegradable, compostable, and recyclable materials</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-clock fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Fast Response</h5>
                        <p>Quotes and enquiries handled within 24 hours</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-handshake fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Custom Solutions</h5>
                        <p>Custom packaging solutions for your business needs</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection