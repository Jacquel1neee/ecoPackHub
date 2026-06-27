@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--primary-green);">
                    <i class="fas fa-envelope me-2"></i>Product Enquiry
                </h2>
                <a href="{{ route('product.show', $product) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Product
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green);">
                    <i class="fas fa-box me-2"></i> {{ $product->name }}
                    <span class="text-muted fw-normal ms-2">({{ $product->code }})</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('enquiry.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" 
                                       value="{{ old('company_name', Auth::user()?->company_name ?? '') }}" 
                                       placeholder="Your company name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person *</label>
                                <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                       value="{{ old('contact_person', Auth::user()?->name ?? '') }}" required>
                                @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', Auth::user()?->phone ?? '') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', Auth::user()?->email ?? '') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity Required</label>
                            <input type="text" name="quantity" class="form-control" 
                                   value="{{ old('quantity') }}" placeholder="e.g., 500 pcs, 2 cartons">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Your Message</label>
                            <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" 
                                      placeholder="Please provide any specific requirements or questions...">{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 10px 30px;">
                                <i class="fas fa-paper-plane me-2"></i> Submit Enquiry
                            </button>
                            <a href="{{ route('product.show', $product) }}" class="btn btn-outline-secondary" style="border-radius: 20px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mt-3">
                <div class="card-body text-center" style="background: #f5f0e8; border-radius: 12px;">
                    <p class="mb-0 small">
                        <i class="fas fa-clock me-1" style="color: var(--primary-green);"></i>
                        We typically respond to enquiries within 24 hours.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection