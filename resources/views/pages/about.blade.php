@extends('layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">About Us</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Hero Section -->
        <div class="col-12 text-center mb-4">
            <h1 class="fw-bold" style="color: var(--primary-green);">
                <i class="fas fa-leaf me-2"></i>About EcoPackHub
            </h1>
            <p class="lead text-muted">Sustainable packaging solutions for a greener future</p>
        </div>

        <!-- Company Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-building me-2"></i>Our Company
                    </h4>
                    <p>
                        <strong>EcoPackHub</strong> is a sustainable packaging platform powered by 
                        <strong>Hybrid Infinity Tech Sdn. Bhd.</strong>, a trusted supplier of biodegradable 
                        and paper-based packaging solutions in Malaysia.
                    </p>
                    <p>
                        We are committed to helping food businesses transition to eco-friendly packaging 
                        by providing high-quality, affordable, and sustainable alternatives to single-use plastics.
                    </p>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle me-2" style="color: var(--primary-green);"></i>
                            <span>100% Biodegradable Products</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle me-2" style="color: var(--primary-green);"></i>
                            <span>Compostable &amp; Eco-Friendly Materials</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle me-2" style="color: var(--primary-green);"></i>
                            <span>Food-Grade Certified Packaging</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2" style="color: var(--primary-green);"></i>
                            <span>Fast &amp; Reliable Delivery</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4 text-center" style="background: #f5f0e8; border-radius: 12px;">
                    <i class="fas fa-recycle fa-4x mb-3" style="color: var(--primary-green);"></i>
                    <h4 style="color: var(--primary-green);">Partnering for a Greener Future</h4>
                    <p class="text-muted">
                        Every product you choose helps reduce plastic waste and protect our environment.
                        Join us in making a difference, one package at a time.
                    </p>
                    <a href="{{ route('home') }}#products" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                        <i class="fas fa-box-open me-2"></i> Browse Our Products
                    </a>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="col-12 mt-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-bullseye fa-3x mb-3" style="color: var(--primary-green);"></i>
                            <h5 class="fw-bold">Our Mission</h5>
                            <p class="text-muted">
                                To provide accessible, affordable, and sustainable packaging solutions 
                                that empower food businesses to reduce their environmental footprint.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-eye fa-3x mb-3" style="color: var(--primary-green);"></i>
                            <h5 class="fw-bold">Our Vision</h5>
                            <p class="text-muted">
                                A world where every food business embraces sustainable packaging, 
                                creating a cleaner, greener future for generations to come.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="col-12 text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary" style="border-radius: 20px;">
                <i class="fas fa-arrow-left me-2"></i> Back to Home
            </a>
        </div>
    </div>
</div>
@endsection