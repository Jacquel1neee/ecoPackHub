@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-comments me-2"></i>Enquiry #{{ $enquiry->id }}
        </h2>
        <div>
            <span class="badge bg-{{ $enquiry->statusColor }} me-2">{{ $enquiry->statusLabel }}</span>
            <a href="{{ route('enquiry.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Chat Area -->
        <div class="col-lg-8">
            <!-- Enquiry Info Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green);">
                    <i class="fas fa-info-circle me-2"></i>Product: {{ $enquiry->product_name }}
                    <span class="text-muted fw-normal ms-2">({{ $enquiry->product_code }})</span>
                </div>
                <div class="card-body">
                    <div class="row small">
                        <div class="col-md-4"><strong>Contact:</strong> {{ $enquiry->contact_person }}</div>
                        <div class="col-md-4"><strong>Phone:</strong> {{ $enquiry->phone }}</div>
                        <div class="col-md-4"><strong>Email:</strong> {{ $enquiry->email }}</div>
                        <div class="col-md-6 mt-1"><strong>Company:</strong> {{ $enquiry->company_name ?? 'N/A' }}</div>
                        <div class="col-md-6 mt-1"><strong>Quantity:</strong> {{ $enquiry->quantity ?? 'Not specified' }}</div>
                        <div class="col-12 mt-2">
                            <strong>Initial Message:</strong>
                            <p class="bg-light p-2 rounded mb-0 mt-1">{{ $enquiry->message ?? 'No message provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CHAT MESSAGES ===== -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-comment-dots me-2"></i>Conversation
                    @if($enquiry->status == 'closed')
                        <span class="badge bg-secondary ms-2">Closed</span>
                    @endif
                </div>
                <div class="card-body" id="chat-messages" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                    @if($enquiry->replies->count() > 0)
                        @foreach($enquiry->replies as $reply)
                            <div class="d-flex gap-3 mb-3 {{ $reply->sender_type == 'user' ? 'flex-row-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    @if($reply->sender_type == 'admin')
                                        <i class="fas fa-user-shield fa-2x text-success"></i>
                                    @else
                                        <i class="fas fa-user fa-2x text-primary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1" style="max-width: 75%;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>
                                            @if($reply->sender_type == 'admin')
                                                Admin ({{ $reply->sender_name }})
                                            @else
                                                {{ $reply->sender_name ?? 'You' }}
                                            @endif
                                        </strong>
                                        <small class="text-muted">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div class="p-3 rounded {{ $reply->sender_type == 'user' ? 'bg-primary text-white' : 'bg-white border' }}">
                                        {{ $reply->reply_message }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-muted py-4">No replies yet. Admin will respond soon.</p>
                    @endif
                </div>

                <!-- ===== REPLY FORM (User can reply) ===== -->
                @if($enquiry->status != 'closed')
                    <div class="card-footer bg-white border-top">
                        <form action="{{ route('enquiry.user.reply', $enquiry) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="reply_message" class="form-control" 
                                   placeholder="Type your reply here..." required style="border-radius: 20px;">
                            <button type="submit" class="btn" style="background-color: var(--primary-green); color: #fff; border-radius: 20px; white-space: nowrap;">
                                <i class="fas fa-paper-plane me-1"></i> Send
                            </button>
                        </form>
                    </div>
                @else
                    <div class="card-footer bg-white border-top text-center text-muted">
                        <i class="fas fa-lock me-1"></i> This conversation is closed.
                    </div>
                @endif
            </div>
        </div>

        <!-- Side Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Details
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> <span class="badge bg-{{ $enquiry->statusColor }}">{{ $enquiry->statusLabel }}</span></p>
                    <p><strong>Submitted:</strong> {{ $enquiry->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Last Reply:</strong> {{ $enquiry->last_reply_at ? $enquiry->last_reply_at->diffForHumans() : 'N/A' }}</p>
                    <p><strong>Total Replies:</strong> {{ $enquiry->replies->count() }}</p>
                </div>
            </div>

            @if($enquiry->product && $enquiry->product->image_path)
                <div class="card shadow-sm border-0 rounded-3 mt-3">
                    <div class="card-body text-center">
                        <img src="{{ asset($enquiry->product->image_path) }}" class="img-fluid rounded" style="max-height: 100px;">
                        <p class="mt-2 small"><strong>{{ $enquiry->product_name }}</strong></p>
                        <a href="{{ route('product.show', $enquiry->product) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> View Product
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Auto-scroll to bottom of chat -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection