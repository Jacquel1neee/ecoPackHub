@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-list me-2"></i>My Feedbacks
        </h2>
        <a href="{{ route('pages.contact') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Contact
        </a>
    </div>

    @if($feedbacks->count() > 0)
        <div class="row g-3">
            @foreach($feedbacks as $feedback)
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <strong>{{ $feedback->subject }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $feedback->created_at->format('d M Y') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-{{ $feedback->statusColor }}">
                                        {{ $feedback->statusLabel }}
                                    </span>
                                    @if(!$feedback->is_read && $feedback->status != 'closed')
                                        <span class="badge bg-danger ms-1">New</span>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">
                                        <i class="fas fa-comment-dots me-1"></i> 
                                        {{ $feedback->replies->count() }} replies
                                    </small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('feedback.show', $feedback) }}" class="btn btn-sm" style="background-color: var(--primary-green); color: #fff; border-radius: 20px;">
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
            <i class="fas fa-comment fa-4x text-muted mb-3"></i>
            <p class="lead">No feedback yet</p>
            <p class="text-muted">Have something to say? Send us your feedback!</p>
            <a href="{{ route('pages.contact') }}" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; padding: 10px 30px;">
                <i class="fas fa-paper-plane me-1"></i> Send Feedback
            </a>
        </div>
    @endif
</div>
@endsection