<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - EcoPackHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-green: #2e7d32;
            --light-green: #4caf50;
            --sidebar-width: 250px;
            --dark: #1a1a1a;
        }
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--primary-green);
            color: #fff;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .brand {
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .brand a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: 0.3s;
            display: block;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px 30px;
            min-height: 100vh;
        }
        .top-bar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-bar .badge-admin {
            background: var(--primary-green);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            color: var(--primary-green);
            opacity: 0.5;
        }
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }
        .stat-card .stat-label {
            color: #888;
            font-size: 0.9rem;
        }
        .card-custom {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: none;
        }
        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            font-weight: 600;
        }
        .card-custom .card-body {
            padding: 20px;
        }
        .btn-green {
            background-color: var(--primary-green);
            color: #fff;
            border: none;
        }
        .btn-green:hover {
            background-color: var(--light-green);
            color: #fff;
        }
        .btn-outline-green {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }
        .btn-outline-green:hover {
            background-color: var(--primary-green);
            color: #fff;
        }
        .product-img-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .alert-custom {
            border-radius: 12px;
            border: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar .brand span,
            .sidebar .nav-link span {
                display: none;
            }
            .main-content {
                margin-left: 60px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-leaf"></i> <span>EcoPackHub</span>
            </a>
        </div>
        <div class="nav flex-column">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            
            <!-- Products -->
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> <span>Products</span>
            </a>
            
            <!-- Categories -->
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> <span>Categories</span>
            </a>
            
            <!-- ===== VENDORS ===== -->
            <a href="{{ route('admin.vendors.index') }}" class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> <span>Vendors</span>
            </a>
            
            <!-- Orders -->
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> <span>Orders</span>
            </a>
            
            <!-- Enquiries -->
            <a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> <span>Enquiries</span>
                @php
                    $unreadCount = \App\Models\EnquiryReply::where('sender_type', 'user')
                        ->where('is_read_by_admin', false)
                        ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.6rem;">{{ $unreadCount }}</span>
                @endif
            </a>
            
            <!-- Feedbacks -->
            <a href="{{ route('admin.feedbacks.index') }}" class="nav-link {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}">
                <i class="fas fa-comment"></i> <span>Feedbacks</span>
                @php
                    $feedbackUnread = \App\Models\FeedbackReply::where('sender_type', 'user')
                        ->where('is_read_by_admin', false)
                        ->count();
                @endphp
                @if($feedbackUnread > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.6rem;">{{ $feedbackUnread }}</span>
                @endif
            </a>

            <!-- Hierarchy (Users) -->
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-sitemap"></i> <span>Hierarchy</span>
            </a>
            
            <!-- View Site -->
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="fas fa-globe"></i> <span>View Site</span>
            </a>
            
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" style="margin-top:20px;">
                @csrf
                <button type="submit" class="nav-link" style="background:none;border:none;width:100%;text-align:left;color:rgba(255,255,255,0.8);">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Admin Panel</h5>
            <div class="user-info">
                <span class="badge-admin">{{ Auth::user()->name ?? 'Admin' }}</span>
                <i class="fas fa-user-circle fa-2x text-secondary"></i>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>