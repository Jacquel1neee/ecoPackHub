@extends('layouts.app')

@section('content')

    <div class="container">
        <!-- ===== PROMOTION EVENT RECOMMENDATIONS ===== -->
        <div class="mb-4">
            <div class="card shadow-sm border-0 rounded-3" style="background: linear-gradient(135deg, #fff8e1, #ffecb3);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #bf8b00;">
                                <i class="fas fa-tags me-2"></i>Recommended for Discount / Promotion Events
                            </h5>
                            <p class="text-muted mb-0">Planning a campaign? Here are product ideas suitable for promotion events.</p>
                        </div>
                        <span class="badge" style="background-color: #ff9800; color: #fff; font-size: 0.75rem;">Event Picks</span>
                    </div>

                    <div class="row g-3 mt-2" id="promotion-suggestions">
                        @php
                            $promoProducts = $allProducts
                                ->filter(fn($product) => $product->has_active_discount)
                                ->shuffle()
                                ->take(4);

                            if ($promoProducts->count() < 4) {
                                $fallbackProducts = $allProducts
                                    ->reject(fn($product) => $promoProducts->contains('id', $product->id))
                                    ->shuffle()
                                    ->take(4 - $promoProducts->count());

                                $promoProducts = $promoProducts->concat($fallbackProducts);
                            }
                        @endphp
                        @if($promoProducts->count() > 0)
                            @foreach($promoProducts as $product)
                                <div class="col-lg-3 col-md-4 col-6">
                                    <div class="card product-card h-100 shadow-sm border-0 rounded-3">
                                        <a href="{{ route('product.show', $product) }}" class="text-decoration-none d-block position-relative" style="overflow: hidden; background: #f8f9fa; height: 150px;">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                            @else
                                                <img src="{{ asset('images/no-image.png') }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                            @endif
                                            <span class="position-absolute top-0 start-0 m-2 badge" style="background-color: #ff9800; color: #fff; font-size: 0.65rem;">
                                                Promo Pick
                                            </span>
                                        </a>
                                        <div class="card-body p-2">
                                            <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                                <h6 class="card-title fw-semibold mb-1 text-truncate" style="font-size: 0.8rem;">{{ $product->name }}</h6>
                                            </a>
                                            @php
                                                $basePrice = (float) $product->min_price;
                                                $promoPrice = (float) $product->discounted_min_price;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if($product->has_active_discount)
                                                        <small class="text-muted text-decoration-line-through">RM {{ number_format($basePrice, 2) }}</small><br>
                                                        <span class="fw-bold" style="color: #e65100; font-size: 0.9rem;">RM {{ number_format($promoPrice, 2) }}</span>
                                                    @else
                                                        <span class="fw-bold" style="color: #e65100; font-size: 0.9rem;">RM {{ number_format($basePrice, 2) }}</span>
                                                    @endif
                                                </div>
                                                @if($product->has_active_discount)
                                                    @if($product->discount_percentage !== null)
                                                        <small class="badge bg-danger" style="font-size: 0.6rem;">-{{ number_format((float) $product->discount_percentage, 0) }}%</small>
                                                    @else
                                                        <small class="badge bg-danger" style="font-size: 0.6rem;">Promo</small>
                                                    @endif
                                                @else
                                                    <small class="badge bg-secondary" style="font-size: 0.6rem;">No active discount</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center text-muted">
                                <p class="mb-0">No products available for promotion recommendations yet.</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 text-end">
                        <small class="text-muted">Need custom promo pricing? Contact us via email or WhatsApp for event quotations.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">            
            <div class="col-lg-3 col-md-4">
                <!-- Categories Card -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green); color: var(--primary-green);">
                        <i class="fas fa-th-large me-2"></i>All Categories
                    </div>
                    <div class="list-group list-group-flush" id="category-list">
                        @php
                            $allCategories = $categories->toArray();
                            $firstFiveCategories = array_slice($allCategories, 0, 5);
                            $remainingCategories = array_slice($allCategories, 5);
                        @endphp
                        
                        <!-- All Products -->
                        <a href="{{ route('home') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-3 {{ !request()->has('category') ? 'active' : '' }}" @if(!request()->has('category')) style="background-color: #e8f5e9;" @endif>
                            <span>
                                <i class="fas fa-box me-2" style="color: var(--primary-green); width: 20px;"></i>
                                All Products
                            </span>
                            <span class="badge bg-secondary rounded-pill">{{ $totalProductsCount }}</span>
                        </a>
                        
                        <!-- First 5 Categories -->
                        @foreach($firstFiveCategories as $category)
                            <a href="{{ route('home', ['category' => $category['slug']]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-3 {{ request()->get('category') == $category['slug'] ? 'active' : '' }}" @if(request()->get('category') == $category['slug']) style="background-color: #e8f5e9;" @endif>
                                <span>
                                    <i class="{{ $category['icon'] ?? 'fa-solid fa-box' }} me-2" style="color: var(--primary-green); width: 20px;"></i>
                                    {{ $category['name'] }}
                                </span>
                                <span class="badge bg-secondary rounded-pill">{{ $category['products_count'] ?? 0 }}</span>
                            </a>
                        @endforeach
                        
                        <!-- Remaining Categories (hidden by default) -->
                        <div id="category-extra" style="display: none;">
                            @foreach($remainingCategories as $category)
                                <a href="{{ route('home', ['category' => $category['slug']]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-3 {{ request()->get('category') == $category['slug'] ? 'active' : '' }}" @if(request()->get('category') == $category['slug']) style="background-color: #e8f5e9;" @endif>
                                    <span>
                                        <i class="{{ $category['icon'] ?? 'fa-solid fa-box' }} me-2" style="color: var(--primary-green); width: 20px;"></i>
                                        {{ $category['name'] }}
                                    </span>
                                    <span class="badge bg-secondary rounded-pill">{{ $category['products_count'] ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                        
                        <!-- Show More/Less Link -->
                        @if(count($remainingCategories) > 0)
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 py-2 px-3 text-center" 
                               id="category-toggle" style="color: var(--primary-green); font-size: 0.85rem; background: transparent;"
                               onclick="toggleSection('category')">
                                <span id="category-toggle-text">+ Show More ({{ count($remainingCategories) }})</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter: Material -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-tag me-2"></i>Material
                    </div>
                    <div class="card-body" id="material-list">
                        @php
                            $allMaterials = $allProducts->pluck('material')->unique()->filter()->values();
                            $firstFiveMaterials = $allMaterials->slice(0, 5);
                            $remainingMaterials = $allMaterials->slice(5);
                        @endphp
                        
                        @forelse($firstFiveMaterials as $material)
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
                        
                        <!-- Remaining Materials -->
                        <div id="material-extra" style="display: none;">
                            @foreach($remainingMaterials as $material)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="material_{{ Str::slug($material) }}" 
                                           {{ request()->get('material') && in_array($material, explode(',', request()->get('material'))) ? 'checked' : '' }}
                                           onchange="applyFilter('material', '{{ $material }}')">
                                    <label class="form-check-label" for="material_{{ Str::slug($material) }}">
                                        {{ $material }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($remainingMaterials->count() > 0)
                            <a href="javascript:void(0)" class="d-block text-center mt-1" 
                               id="material-toggle" style="color: var(--primary-green); font-size: 0.85rem; text-decoration: none;"
                               onclick="toggleSection('material')">
                                <span id="material-toggle-text">+ Show More ({{ $remainingMaterials->count() }})</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter: Size -->
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-header bg-white py-3 fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-ruler me-2"></i>Size
                    </div>
                    <div class="card-body" id="size-list">
                        @php
                            $allSizes = [];
                            foreach ($allProducts as $product) {
                                foreach ($product->variants as $variant) {
                                    if ($variant->size) {
                                        $allSizes[] = $variant->size;
                                    }
                                }
                            }
                            $allSizes = collect($allSizes)->unique()->filter()->values();
                            $firstFiveSizes = $allSizes->slice(0, 5);
                            $remainingSizes = $allSizes->slice(5);
                        @endphp
                        
                        @forelse($firstFiveSizes as $size)
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
                        
                        <!-- Remaining Sizes -->
                        <div id="size-extra" style="display: none;">
                            @foreach($remainingSizes as $size)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="size_{{ Str::slug($size) }}"
                                           {{ request()->get('size') && in_array($size, explode(',', request()->get('size'))) ? 'checked' : '' }}
                                           onchange="applyFilter('size', '{{ $size }}')">
                                    <label class="form-check-label" for="size_{{ Str::slug($size) }}">
                                        {{ $size }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($remainingSizes->count() > 0)
                            <a href="javascript:void(0)" class="d-block text-center mt-1" 
                               id="size-toggle" style="color: var(--primary-green); font-size: 0.85rem; text-decoration: none;"
                               onclick="toggleSection('size')">
                                <span id="size-toggle-text">+ Show More ({{ $remainingSizes->count() }})</span>
                            </a>
                        @endif
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
                                <span id="cart-badge" class="badge bg-light text-dark ms-1 rounded-pill" style="font-size: 0.7rem;" data-cart-count="{{ $cartCount ?? 0 }}">{{ $cartCount ?? 0 }}</span>
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
                    <div class="row g-4">
                        @foreach($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                                <div class="card product-card h-100 shadow-sm border-0 rounded-3 overflow-hidden" style="transition: transform 0.2s;">
                                    <!-- Product Image -->
                                    <a href="{{ route('product.show', $product) }}" class="text-decoration-none d-block position-relative" style="overflow: hidden; background: #f8f9fa; height: 200px;">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-100 h-100" alt="{{ $product->name }}" style="object-fit: contain; object-position: center;">
                                        @endif
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 small px-2 py-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                            {{ $product->code }}
                                        </span>
                                        <span class="position-absolute bottom-0 start-0 m-2 badge bg-primary bg-opacity-75 small px-2 py-1" style="font-size: 0.6rem;">
                                            <i class="fas fa-layer-group me-1"></i> {{ $product->variants->count() }}
                                        </span>
                                    </a>
                                    
                                    <div class="card-body d-flex flex-column p-3">
                                        <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                            <h6 class="card-title fw-semibold mb-1" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.4rem;">
                                                {{ $product->name }}
                                            </h6>
                                        </a>
                                        
                                        <p class="card-text small text-muted mb-2" style="display: -webkit-box; -webkit-line-clamp: 1; line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; min-height: 1.2rem;">
                                            {{ Str::limit($product->description, 40) }}
                                        </p>
                                        
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold" style="color: var(--primary-green); font-size: 1rem;">
                                                    @if($product->has_active_discount)
                                                        <small class="text-muted fw-normal text-decoration-line-through" style="font-size: 0.7rem;">RM {{ number_format($product->min_price, 2) }}@if($product->min_price != $product->max_price) - RM {{ number_format($product->max_price, 2) }}@endif</small>
                                                        <br>
                                                        RM {{ number_format($product->discounted_min_price, 2) }}
                                                        @if($product->discounted_min_price != $product->discounted_max_price)
                                                            <small class="text-muted fw-normal" style="font-size: 0.7rem;">- RM {{ number_format($product->discounted_max_price, 2) }}</small>
                                                        @endif
                                                    @else
                                                        RM {{ number_format($product->min_price, 2) }}
                                                        @if($product->min_price != $product->max_price)
                                                            <small class="text-muted fw-normal" style="font-size: 0.7rem;">- RM {{ number_format($product->max_price, 2) }}</small>
                                                        @endif
                                                    @endif
                                                </span>
                                                <small class="text-muted" style="font-size: 0.6rem;">{{ $product->variants->first()->packing_quantity ?? '' }}</small>
                                            </div>
                                            
                                            <div class="mt-1" style="min-height: 1.2rem;">
                                                @if($product->material)
                                                    <span class="badge bg-light text-dark me-1" style="font-size: 0.55rem; font-weight: 400; padding: 2px 6px;">
                                                        <i class="fas fa-tag" style="font-size: 0.5rem;"></i> {{ $product->material }}
                                                    </span>
                                                @endif
                                                @php
                                                    $sizesList = $product->variants->pluck('size')->filter()->unique()->values();
                                                @endphp
                                                @if($sizesList->count() > 0)
                                                    <span class="badge bg-light text-dark" style="font-size: 0.55rem; font-weight: 400; padding: 2px 6px;">
                                                        <i class="fas fa-ruler" style="font-size: 0.5rem;"></i> 
                                                        {{ $sizesList->first() }}
                                                        @if($sizesList->count() > 1)
                                                            +{{ $sizesList->count() - 1 }}
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @auth
                                                <a href="{{ route('product.show', $product) }}" class="btn btn-sm w-100 mt-2" 
                                                   style="background-color: var(--primary-green); color: #fff; border-radius: 20px; border: none; text-decoration: none; display: inline-block; text-align: center; padding: 5px 0; font-size: 0.75rem; transition: background 0.2s;">
                                                    <i class="fas fa-cart-plus me-1"></i> Select Options
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-sm w-100 mt-2" 
                                                   style="background-color: var(--primary-green); color: #fff; border-radius: 20px; text-decoration: none; display: inline-block; text-align: center; padding: 5px 0; font-size: 0.75rem;">
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

        <!-- ===== AI SUGGESTION SECTION ===== -->
        <div class="mb-4">
            <div class="card shadow-sm border-0 rounded-3" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fw-bold mb-1" style="color: var(--primary-green);">
                                <i class="fas fa-lightbulb me-2"></i>Suggestion
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
                                        </a>
                                        <div class="card-body p-2">
                                            <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-dark">
                                                <h6 class="card-title fw-semibold mb-0 text-truncate" style="font-size: 0.8rem;">{{ $product->name }}</h6>
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="fw-bold" style="color: var(--primary-green); font-size: 0.85rem;">
                                                    @if($product->has_active_discount)
                                                        <small class="text-muted text-decoration-line-through me-1">RM {{ number_format($product->min_price, 2) }}</small>
                                                        RM {{ number_format($product->discounted_min_price, 2) }}
                                                    @else
                                                        RM {{ number_format($product->min_price, 2) }}
                                                    @endif
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

        <!-- ===== ADVANTAGES SECTION (Full Width) ===== -->
        <section class="mt-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 text-center" style="background: #f8f9fa; border-radius: 12px; height: 100%;">
                        <i class="fas fa-recycle fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5 style="font-size: 1rem; font-weight: 600;">Sustainable Materials</h5>
                        <p class="mb-0 small" style="color: #6c757d; line-height: 1.6;">All products are made from biodegradable, compostable, and recyclable materials</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 text-center" style="background: #f8f9fa; border-radius: 12px; height: 100%;">
                        <i class="fas fa-clock fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5 style="font-size: 1rem; font-weight: 600;">Fast Response</h5>
                        <p class="mb-0 small" style="color: #6c757d; line-height: 1.6;">Quotes and enquiries handled within 24 hours</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 text-center" style="background: #f8f9fa; border-radius: 12px; height: 100%;">
                        <i class="fas fa-handshake fa-3x mb-3" style="color: var(--primary-green);"></i>
                        <h5 style="font-size: 1rem; font-weight: 600;">Custom Solutions</h5>
                        <p class="mb-0 small" style="color: #6c757d; line-height: 1.6;">Custom packaging solutions for your business needs</p>
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

        // ===== TOGGLE FUNCTION =====
        function toggleSection(type) {
            const extra = document.getElementById(type + '-extra');
            const toggleText = document.getElementById(type + '-toggle-text');
            
            if (extra.style.display === 'none') {
                extra.style.display = 'block';
                toggleText.textContent = '− Show Less';
            } else {
                extra.style.display = 'none';
                const items = extra.querySelectorAll('.form-check, .list-group-item');
                toggleText.textContent = '+ Show More (' + items.length + ')';
            }
        }

        // ===== FETCH CART COUNT =====
        function updateCartBadges(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            }

            const navBadge = document.getElementById('nav-cart-badge');
            if (navBadge) {
                navBadge.textContent = count;
                navBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('cart-badge');
            const initialCartCount = badge ? Number(badge.dataset.cartCount || 0) : 0;

            updateCartBadges(initialCartCount);
            if (badge) {
                fetchCartCount();
            }
        });

        function fetchCartCount() {
            fetch('{{ route("cart.count") }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count !== undefined) {
                    updateCartBadges(Number(data.count || 0));
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
        }

        function hideToast() {
            document.getElementById('cart-toast').style.display = 'none';
        }
    </script>
@endsection