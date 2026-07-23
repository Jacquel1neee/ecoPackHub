<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoPackHub - Sustainable Packaging Solutions</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        :root {
            --primary-green: #2e7d32;
            --light-green: #4caf50;
            --cream: #f5f0e8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Navbar */
        .navbar-custom {
            background-color: var(--primary-green);
        }
        .navbar-custom .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .navbar-custom .navbar-brand:hover {
            color: #fff;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.85);
        }
        .navbar-custom .nav-link:hover {
            color: #fff;
        }
        .navbar-custom .btn-outline-light {
            border-color: rgba(255,255,255,0.5);
            color: #fff;
        }
        .navbar-custom .btn-outline-light:hover {
            background-color: #fff;
            color: var(--primary-green);
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: #fff;
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 30px 30px;
        }
        .hero h1 {
            font-size: 2.8rem;
            font-weight: 700;
        }
        .hero .btn-primary-custom {
            background-color: #fff;
            color: var(--primary-green);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            border: none;
        }
        .hero .btn-primary-custom:hover {
            background-color: #f0f0f0;
        }

        /* Product Card */
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
        }

        /* Sidebar Category Items */
        .list-group-item-action {
            transition: background-color 0.2s;
        }
        .list-group-item-action:hover {
            background-color: #e8f5e9;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Footer */
        .footer {
            background-color: #1a1a1a;
            color: #ccc;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        .footer h5 {
            color: #fff;
            font-weight: 600;
        }
        .footer a {
            color: #aaa;
            text-decoration: none;
        }
        .footer a:hover {
            color: #fff;
        }
        .footer .copyright {
            border-top: 1px solid #333;
            padding-top: 20px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        /* Scroll margin for fixed navbar */
        #products {
            scroll-margin-top: 80px;
        }
        #contact {
            scroll-margin-top: 80px;
        }
        #about {
            scroll-margin-top: 80px;
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/HiTechEcoPack.png') }}" alt="EcoPackHub Logo" style="height: 38px; width: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">
                        <i class="fas fa-box me-1"></i>Products
                    </a>
                </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}">
                                <i class="fas fa-receipt me-1"></i>My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mailto:hitech7785@gmail.com">
                                <i class="fas fa-envelope me-1"></i>Email Enquiry
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://wa.me/60122210442" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('feedback.history') }}">
                                <i class="fas fa-comment me-1"></i>My Feedbacks
                            </a>
                        </li>
                        @if(Auth::user()->role == 1)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-user-shield me-1"></i>Admin
                                </a>
                            </li>
                        @endif
                    @endauth

                </ul>
                <form action="{{ route('products.index') }}" method="GET" class="d-flex me-3">
                    <input class="form-control form-control-sm me-2" type="search" name="search" 
                        placeholder="Search products..." style="border-radius: 20px;"
                        value="{{ request('search') }}">
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                @auth
                    <span class="text-white me-2">Hi, {{ Auth::user()->name }}</span>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm me-2" style="position: relative;">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="nav-cart-badge" class="badge bg-danger rounded-pill" style="position: absolute; top: -8px; right: -8px; font-size: 0.6rem; padding: 2px 6px; display: none;">0</span>
                    </a>

                    {{-- Notifications bell --}}
                    @auth
                        <div class="d-inline-block ms-1">
                            @php
                                echo view('partials.notifications_dropdown', [
                                    'notifications' => App\Models\UserNotification::where('notifiable_type', get_class(Auth::user()))->where('notifiable_id', Auth::id())->orderByDesc('created_at')->limit(20)->get(),
                                    'unreadCount' => App\Models\UserNotification::where('notifiable_type', get_class(Auth::user()))->where('notifiable_id', Auth::id())->whereNull('read_at')->count(),
                                ]);
                            @endphp
                        </div>
                    @endauth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-user"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm text-success">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ===== CONTENT ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>
                        <img src="{{ asset('images/HiTechEcoPack.png') }}" alt="EcoPackHub Logo" style="height: 28px; width: auto;">
                    </h5>
                    <p>Your trusted partner for sustainable biodegradable and paper-based packaging solutions in Malaysia.</p>
                    <p><small>Hybrid Infinity Tech Sdn. Bhd.</small></p>
                </div>
                <div class="col-md-2 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}#products">Products</a></li>
                        <li><a href="{{ route('pages.about') }}">About Us</a></li>
                        <li><a href="{{ route('pages.contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>+6012-221 0442</li>
                        <li><i class="fas fa-envelope me-2"></i>hitech7785@gmail.com</li>
                        <li><i class="fas fa-whatsapp me-2"></i><a href="https://wa.me/60122210442" target="_blank">WhatsApp Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Follow Us</h5>
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
            <div class="copyright text-center">
                &copy; 2026 EcoPackHub. All rights reserved. <span class="text-success">&#9829;</span> Sustainable Packaging.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ===== CART COUNT JAVASCRIPT ===== -->
    @auth
        <script>
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
                    const badge = document.getElementById('nav-cart-badge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));
            }
        </script>
    @endauth
</body>
</html>