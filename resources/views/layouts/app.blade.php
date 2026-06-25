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
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-leaf me-2"></i>EcoPackHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#products"><i class="fas fa-box me-1"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-phone me-1"></i>Contact</a>
                    </li>
                    @auth
                        @if(Auth::user()->role == 1)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-user-shield me-1"></i>Admin
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <form class="d-flex me-3">
                    <input class="form-control form-control-sm me-2" type="search" placeholder="Search products..." style="border-radius: 20px;">
                    <button class="btn btn-outline-light btn-sm" type="submit"><i class="fas fa-search"></i></button>
                </form>
                @auth
                    <span class="text-white me-2">Hi, {{ Auth::user()->name }}</span>
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
                    <h5><i class="fas fa-leaf me-2"></i>EcoPackHub</h5>
                    <p>Your trusted partner for sustainable biodegradable and paper-based packaging solutions in Malaysia.</p>
                    <p><small>Hybrid Infinity Tech Sdn. Bhd.</small></p>
                </div>
                <div class="col-md-2 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#products">Products</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>+6012-221 0442</li>
                        <li><i class="fas fa-envelope me-2"></i>hitech7785@gmail.com</li>
                        <li><i class="fas fa-whatsapp me-2"></i><a href="#" target="_blank">WhatsApp Us</a></li>
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
</body>
</html>