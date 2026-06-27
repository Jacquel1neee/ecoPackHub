@extends('layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Contact Us</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Header -->
        <div class="col-12 text-center mb-3">
            <h1 class="fw-bold" style="color: var(--primary-green);">
                <i class="fas fa-phone me-2"></i>Contact Us
            </h1>
            <p class="lead text-muted">We'd love to hear from you! Reach out with any questions or inquiries.</p>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-info-circle me-2"></i>Get in Touch
                    </h4>
                    <p class="text-muted">
                        Have questions about our products, need a custom quote, or want to discuss 
                        sustainable packaging solutions? Contact us today!
                    </p>

                    <div class="mt-4">
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-map-marker-alt mt-1 me-3" style="color: var(--primary-green); font-size: 1.2rem; width: 24px;"></i>
                            <div>
                                <strong>Address</strong>
                                <p class="mb-0 text-muted">141, Menara Mutiara Majestic,<br>Jln Othman, Petaling Jaya Old Town,<br>46000 Petaling Jaya, Selangor</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-phone mt-1 me-3" style="color: var(--primary-green); font-size: 1.2rem; width: 24px;"></i>
                            <div>
                                <strong>Phone</strong>
                                <p class="mb-0 text-muted">
                                    <a href="tel:+60122210442" style="color: var(--primary-green); text-decoration: none;">+6012-221 0442</a> (Dr. Sam)
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-envelope mt-1 me-3" style="color: var(--primary-green); font-size: 1.2rem; width: 24px;"></i>
                            <div>
                                <strong>Email</strong>
                                <p class="mb-0 text-muted">
                                    <a href="mailto:hitech7785@gmail.com" style="color: var(--primary-green); text-decoration: none;">hitech7785@gmail.com</a>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <i class="fab fa-whatsapp mt-1 me-3" style="color: var(--primary-green); font-size: 1.2rem; width: 24px;"></i>
                            <div>
                                <strong>WhatsApp</strong>
                                <p class="mb-0 text-muted">
                                    <a href="https://wa.me/60122210442" target="_blank" style="color: var(--primary-green); text-decoration: none;">+6012-221 0442</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <h5 class="fw-bold"><i class="fas fa-clock me-2"></i>Business Hours</h5>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Monday - Friday</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Saturday</span>
                            <span>10:00 AM - 2:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Sunday &amp; Public Holidays</span>
                            <span class="text-muted">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h4 class="fw-bold" style="color: var(--primary-green);">
                        <i class="fas fa-paper-plane me-2"></i>Send Us Feedback
                    </h4>
                    <p class="text-muted">We'll get back to you within 24 hours.</p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required placeholder="John Doe">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required placeholder="john@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" required placeholder="FeedBack">
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required placeholder="Write your message here...">{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn w-100" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 12px;">
                            <i class="fas fa-paper-plane me-2"></i> Send Feedback
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <p class="text-muted small">
                            <i class="fas fa-shield-alt me-1"></i> Your information is safe with us.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Contact Buttons -->
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <a href="tel:+60122210442" class="btn w-100" style="border: 2px solid var(--primary-green); color: var(--primary-green); border-radius: 20px; padding: 10px;">
                        <i class="fas fa-phone me-2"></i> Call Us
                    </a>
                </div>
                <div class="col-6">
                    <a href="https://wa.me/60122210442" target="_blank" class="btn w-100" style="background-color: #25D366; color: #fff; border-radius: 20px; padding: 10px;">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                    </a>
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