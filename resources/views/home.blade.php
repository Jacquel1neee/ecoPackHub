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
        <!-- ===== SHOPEE-STYLE: SIDEBAR FILTER + PRODUCTS ===== -->
        <div class="row g-4">
            
            <!-- ===== LEFT SIDEBAR: CATEGORIES + FILTERS ===== -->
            <div class="col-lg-3 col-md-4">
                <!-- Categories Card -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green); color: var(--primary-green);">
                        <i class="fas fa-th-large me-2"></i>All Categories
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('home') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-3 {{ !request()->has('category') ? 'active' : '' }}" @if(!request()->has('category')) style="background-color: #e8f5e9;" @endif>
                            <span>
                                <i class="fas fa-box me-2" style="color: var(--primary-green); width: 20px;"></i>
                                All Products
                            </span>
                            <span class="badge bg-secondary rounded-pill">{{ $totalProductsCount }}</span>
                        </a>
                        @forelse($categories as $category)
                            <a href="{{ route('home', ['category' => $category->slug]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-3 {{ request()->get('category') == $category->slug ? 'active' : '' }}" @if(request()->get('category') == $category->slug) style="background-color: #e8f5e9;" @endif>
                                <span>
                                    <i class="{{ $category->icon ?? 'fa-solid fa-box' }} me-2" style="color: var(--primary-green); width: 20px;"></i>
                                    {{ $category->name }}
                                </span>
                                <span class="badge bg-secondary rounded-pill">{{ $category->products_count ?? 0 }}</span>
                            </a>
                        @empty
                            <div class="text-center text-muted py-3">
                                <p class="mb-0 small">No categories available.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Filter: Material -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-tag me-2"></i>Material
                    </div>
                    <div class="card-body">
                        @php
                            $materials = $allProducts->pluck('material')->unique()->filter()->values();
                        @endphp
                        @forelse($materials as $material)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="material_{{ Str::slug($material) }}" 
                                       {{ request()->get('material') && in_array($material, explode(',', request()->get('material'))) ? 'checked' : '' }}
                                       onchange="applyFilter('material', '{{ $material }}')">
                                <label class="form-check-label" for="material_{{ Str::slug($material) }}">
                                    {{ $material }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No materials available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Filter: Size -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-ruler me-2"></i>Size
                    </div>
                    <div class="card-body">
                        @php
                            // Get sizes from variants
                            $sizes = [];
                            foreach ($allProducts as $product) {
                                foreach ($product->variants as $variant) {
                                    if ($variant->size) {
                                        $sizes[] = $variant->size;
                                    }
                                }
                            }
                            $sizes = collect($sizes)->unique()->filter()->values();
                        @endphp
                        @forelse($sizes as $size)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="size_{{ Str::slug($size) }}"
                                       {{ request()->get('size') && in_array($size, explode(',', request()->get('size'))) ? 'checked' : '' }}
                                       onchange="applyFilter('size', '{{ $size }}')">
                                <label class="form-check-label" for="size_{{ Str::slug($size) }}">
                                    {{ $size }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No sizes available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Filter: Price Range -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-dollar-sign me-2"></i>Price Range
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted">Min (RM)</label>
                                <input type="number" class="form-control form-control-sm" id="price_min" placeholder="0" 
                                       value="{{ request()->get('price_min') }}" min="0">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Max (RM)</label>
                                <input type="number" class="form-control form-control-sm" id="price_max" placeholder="999" 
                                       value="{{ request()->get('price_max') }}" min="0">
                            </div>
                        </div>
                        <button class="btn btn-sm w-100 mt-2" style="background-color: var(--primary-green); color: #fff;" onclick="applyPriceFilter()">
                            <i class="fas fa-search me-1"></i> Apply Price
                        </button>
                    </div>
                </div>

                <!-- Clear Filters -->
                <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="fas fa-undo me-1"></i> Clear All Filters
                </a>

                <!-- Promo Banner -->
                <div class="card shadow-sm border-0 rounded-3 mt-3 d-none d-md-block">
                    <div class="card-body text-center" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-leaf fa-2x mb-2" style="color: var(--primary-green);"></i>
                        <p class="mb-0 small">Eco-friendly packaging<br>solutions for your business</p>
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT CONTENT: PRODUCTS ===== -->
            <div class="col-lg-9 col-md-8">
                
                <!-- Product Header with Cart Link -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-box me-2"></i>Our Products
                    </h4>
                    <div>
                        <span class="text-muted small me-3">{{ $products->count() }} products found</span>
                        @auth
                            <a href="{{ route('cart.index') }}" class="btn btn-sm" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                                <i class="fas fa-shopping-cart me-1"></i> View Cart
                                <span id="cart-badge" class="badge bg-light text-dark ms-1 rounded-pill" style="font-size: 0.7rem;">0</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Toast Notification -->
                <div id="cart-toast" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Added to Cart</strong>
                            <button type="button" class="btn-close btn-close-white" onclick="hideToast()"></button>
                        </div>
                        <div class="toast-body" id="toast-message">
                            Product added to cart!
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                @if($products->count() > 0)
                    <div class="row g-3">
                        @foreach($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                                <div class="card product-card h-100 shadow-sm border-0 rounded-3">
                                    <!-- Product Image - Clickable to Detail -->
                                    <a href="{{ route('product.show', $product) }}" class="text-decoration-none">
                                        <div class="position-relative">
                                            @if($product->image_path)
                                                <img src="{{ asset($product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                            @else
                                                <img src="https://via.placeholder.com/300x200/2e7d32/ffffff?text={{ urlencode($product->code) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                            @endif
                                            <!-- Code Badge -->
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 small">
                                                {{ $product->code }}
                                            </span>
                                            <!-- Variant Count Badge -->
                                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-primary bg-opacity-75 small">
                                                <i class="fas fa-layer-group me-1"></i> {{ $product->variants->count() }} variants
                                            </span>
                                        </div>
                                    </a>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Name - Clickable to Detail -->
                                        <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                            <h6 class="card-title fw-semibold mb-1 text-truncate">{{ $product->name }}</h6>
                                        </a>
                                        
                                        <p class="card-text small text-muted mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ Str::limit($product->description, 50) }}
                                        </p>
                                        
                                        <div class="mt-auto">
                                            <!-- Price - Show Min Price -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold" style="color: var(--primary-green); font-size: 1.1rem;">
                                                    RM {{ number_format($product->min_price, 2) }}
                                                    @if($product->min_price != $product->max_price)
                                                        <small class="text-muted fw-normal">- RM {{ number_format($product->max_price, 2) }}</small>
                                                    @endif
                                                </span>
                                                <small class="text-muted">{{ $product->variants->first()->packing_quantity ?? '' }}</small>
                                            </div>
                                            
                                            <!-- Material & Size Tags -->
                                            <div class="mt-1">
                                                @if($product->material)
                                                    <span class="badge bg-light text-dark me-1 small fw-normal">
                                                        <i class="fas fa-tag"></i> {{ $product->material }}
                                                    </span>
                                                @endif
                                                @php
                                                    $sizesList = $product->variants->pluck('size')->filter()->unique()->values();
                                                @endphp
                                                @if($sizesList->count() > 0)
                                                    <span class="badge bg-light text-dark small fw-normal">
                                                        <i class="fas fa-ruler"></i> {{ $sizesList->first() }}
                                                        @if($sizesList->count() > 1)
                                                            +{{ $sizesList->count() - 1 }} more
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- ===== SELECT OPTIONS BUTTON (goes to detail page) ===== -->
                                            @auth
                                                <a href="{{ route('product.show', $product) }}" class="btn btn-sm w-100 mt-2" 
                                                   style="background-color: var(--primary-green); color: #fff; border-radius: 20px; border: none; text-decoration: none; display: inline-block; text-align: center;">
                                                    <i class="fas fa-cart-plus me-1"></i> Select Options
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-sm w-100 mt-2" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; text-decoration: none; display: inline-block; text-align: center;">
                                                    <i class="fas fa-sign-in-alt me-1"></i> Login to Buy
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-box-open fa-3x d-block mb-3"></i>
                        <p>No products found matching your filters.</p>
                        <a href="{{ route('home') }}" class="btn btn-sm" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                            <i class="fas fa-undo me-1"></i> Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- ===== ADVANTAGES SECTION (Full Width) ===== -->
        <section class="mt-5">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-recycle fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Sustainable Materials</h5>
                        <p class="mb-0">All products are made from biodegradable, compostable, and recyclable materials</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-clock fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Fast Response</h5>
                        <p class="mb-0">Quotes and enquiries handled within 24 hours</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4" style="background: #f5f0e8; border-radius: 12px;">
                        <i class="fas fa-handshake fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5>Custom Solutions</h5>
                        <p class="mb-0">Custom packaging solutions for your business needs</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ===== FILTER JAVASCRIPT ===== -->
    <script>
        function applyFilter(type, value) {
            const currentUrl = new URL(window.location.href);
            let existing = currentUrl.searchParams.get(type);
            
            if (existing) {
                let values = existing.split(',');
                if (values.includes(value)) {
                    values = values.filter(v => v !== value);
                } else {
                    values.push(value);
                }
                if (values.length > 0) {
                    currentUrl.searchParams.set(type, values.join(','));
                } else {
                    currentUrl.searchParams.delete(type);
                }
            } else {
                currentUrl.searchParams.set(type, value);
            }
            
            window.location.href = currentUrl.toString();
        }

        function applyPriceFilter() {
            const min = document.getElementById('price_min').value;
            const max = document.getElementById('price_max').value;
            const currentUrl = new URL(window.location.href);
            
            if (min && min > 0) {
                currentUrl.searchParams.set('price_min', min);
            } else {
                currentUrl.searchParams.delete('price_min');
            }
            
            if (max && max > 0) {
                currentUrl.searchParams.set('price_max', max);
            } else {
                currentUrl.searchParams.delete('price_max');
            }
            
            window.location.href = currentUrl.toString();
        }

        // ===== FETCH CART COUNT =====
        document.addEventListener('DOMContentLoaded', function() {
            fetchCartCount();
        });

        function fetchCartCount() {
            fetch('{{ route("cart.count") }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('cart-badge');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
        }
    </script>
@endsection