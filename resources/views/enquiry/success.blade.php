@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x" style="color: var(--primary-green);"></i>
                    </div>
                    <h2 class="fw-bold mb-3" style="color: var(--primary-green);">Enquiry Submitted!</h2>
                    <p class="text-muted mb-4">
                        Thank you for your enquiry. We have received your request and will get back to you within 24 hours.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 10px 30px;">
                            <i class="fas fa-home me-2"></i> Back to Home
                        </a>
                        @auth
                            <a href="{{ route('enquiry.history') }}" class="btn btn-outline-secondary" style="border-radius: 20px; padding: 10px 30px;">
                                <i class="fas fa-list me-2"></i> My Enquiries
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection