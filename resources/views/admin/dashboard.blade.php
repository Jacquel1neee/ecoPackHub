@extends('admin.layouts.app')

@section('content')
    <!-- ===== STATS CARDS ===== -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Products</div>
                        <div class="stat-number">{{ $totalProducts }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Categories</div>
                        <div class="stat-number">{{ $totalCategories }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-th-large"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Vendors</div>
                        <div class="stat-number">{{ $totalVendors ?? 0 }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-number">RM {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ring"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECOND ROW STATS ===== -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Sales</div>
                        <div class="stat-number">{{ number_format($totalSales) }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-number">{{ $totalOrders ?? 0 }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Active Vendors</div>
                        <div class="stat-number">{{ $activeVendors ?? 0 }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-number">{{ $totalUsers ?? 0 }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CHART + TOP PRODUCTS ===== -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2 text-success"></i> Sales Revenue (Last 30 Days)
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="fas fa-trophy me-2 text-warning"></i> Top Products
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($topProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->code }}</small>
                                </div>
                                <span class="badge bg-success rounded-pill">{{ $product->sales_sum_quantity_sold ?? 0 }} sold</span>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3">No sales data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RECENT PRODUCTS WITH VENDORS ===== -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clock me-2 text-primary"></i> Recent Products & Vendors</span>
                    <div>
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-truck me-1"></i> Vendors
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-green">
                            <i class="fas fa-arrow-right"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Vendors & Prices</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProducts as $product)
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
                                        @if($product->vendors && $product->vendors->count() > 0)
                                            @foreach($product->vendors as $vendor)
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-info">{{ $vendor->name }}</span>
                                                    <span class="small">RM {{ number_format($vendor->pivot->price, 2) }}</span>
                                                    @if($vendor->pivot->is_preferred)
                                                        <span class="badge bg-warning text-dark">★</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No vendors</span>
                                        @endif
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
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
    </div>

    <!-- ===== VENDOR SUMMARY TABLE ===== -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck me-2 text-success"></i> Vendor Summary</span>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-right"></i> Manage Vendors
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Contact</th>
                                <th>Products</th>
                                <th>Total Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendorSummary ?? [] as $vendor)
                                <tr>
                                    <td><strong>{{ $vendor->name }}</strong></td>
                                    <td>
                                        {{ $vendor->contact_person ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $vendor->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vendor->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        RM {{ number_format($vendor->total_value ?? 0, 2) }}
                                    </td>
                                    <td>
                                        @if($vendor->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-truck fa-2x d-block mb-2"></i>
                                        No vendors found. <a href="{{ route('admin.vendors.create') }}">Add your first vendor</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CHART SCRIPT ===== -->
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($salesData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.date),
                datasets: [{
                    label: 'Daily Revenue (RM)',
                    data: salesData.map(item => item.revenue),
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4caf50',
                    pointBorderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'RM ' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection