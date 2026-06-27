@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-list me-2"></i>My Enquiries
        </h2>
        <a href="{{ route('home') }}#products" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Shopping
        </a>
    </div>

    @if($enquiries->count() > 0)
        <div class="row g-3">
            @foreach($enquiries as $enquiry)
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <strong>{{ $enquiry->product_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $enquiry->product_code }}</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-{{ $enquiry->statusColor }}">
                                        {{ $enquiry->statusLabel }}
                                    </span>
                                    @if(!$enquiry->is_read && $enquiry->status != 'closed')
                                        <span class="badge bg-danger ms-1">New</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $enquiry->created_at->format('d M Y') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Phone: {{ $enquiry->phone }}</small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-comment-dots me-1"></i> 
                                        {{ $enquiry->replies->count() }} replies
                                    </small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('enquiry.show', $enquiry) }}" class="btn btn-sm" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
            <p class="lead">No enquiries yet</p>
            <p class="text-muted">Browse our products and submit an enquiry!</p>
            <a href="{{ route('home') }}#products" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 10px 30px;">
                <i class="fas fa-search me-1"></i> Browse Products
            </a>
        </div>
    @endif
</div>
@endsection