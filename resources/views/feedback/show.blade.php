@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-green);">
            <i class="fas fa-comments me-2"></i>Feedback #{{ $feedback->id }}
        </h2>
        <div>
            <span class="badge bg-{{ $feedback->statusColor }} me-2">{{ $feedback->statusLabel }}</span>
            <a href="{{ route('feedback.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Feedback Info -->
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom: 2px solid var(--primary-green);">
                    <i class="fas fa-info-circle me-2"></i>{{ $feedback->subject }}
                </div>
                <div class="card-body">
                    <div class="row small">
                        <div class="col-md-4"><strong>Name:</strong> {{ $feedback->name }}</div>
                        <div class="col-md-4"><strong>Email:</strong> {{ $feedback->email }}</div>
                        <div class="col-md-4"><strong>Submitted:</strong> {{ $feedback->created_at->format('d M Y, H:i') }}</div>
                        <div class="col-12 mt-2">
                            <strong>Message:</strong>
                            <p class="bg-light p-2 rounded mb-0 mt-1">{{ $feedback->message }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="fas fa-comment-dots me-2"></i>Conversation
                    @if($feedback->status == 'closed')
                        <span class="badge bg-secondary ms-2">Closed</span>
                    @endif
                </div>
                <div class="card-body" id="chat-messages" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                    @if($feedback->replies->count() > 0)
                        @foreach($feedback->replies as $reply)
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

                <!-- Reply Form -->
                @if($feedback->status != 'closed')
                    <div class="card-footer bg-white border-top">
                        <form action="{{ route('feedback.user.reply', $feedback) }}" method="POST" class="d-flex gap-2">
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
                    <p><strong>Status:</strong> <span class="badge bg-{{ $feedback->statusColor }}">{{ $feedback->statusLabel }}</span></p>
                    <p><strong>Submitted:</strong> {{ $feedback->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Last Reply:</strong> {{ $feedback->last_reply_at ? $feedback->last_reply_at->diffForHumans() : 'N/A' }}</p>
                    <p><strong>Total Replies:</strong> {{ $feedback->replies->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection