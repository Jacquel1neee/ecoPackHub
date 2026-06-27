@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-comment me-2"></i>Feedback #{{ $feedback->id }}</h4>
    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Feedback Info -->
        <div class="card card-custom mb-3">
            <div class="card-header">
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
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-comment-dots me-2"></i>Conversation</span>
                <span class="badge bg-{{ $feedback->statusColor }}">{{ $feedback->statusLabel }}</span>
            </div>
            <div class="card-body" id="chat-messages" style="max-height: 450px; overflow-y: auto; background: #f8f9fa;">
                @if($feedback->replies->count() > 0)
                    @foreach($feedback->replies as $reply)
                        <div class="d-flex gap-3 mb-3 {{ $reply->sender_type == 'admin' ? 'flex-row-reverse' : '' }}">
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
                                            {{ $reply->sender_name ?? $feedback->name }}
                                        @endif
                                    </strong>
                                    <small class="text-muted">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <div class="p-3 rounded {{ $reply->sender_type == 'admin' ? 'bg-success text-white' : 'bg-white border' }}">
                                    {{ $reply->reply_message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-muted py-4">No replies yet.</p>
                @endif
            </div>

            <!-- Reply Form -->
            @if($feedback->status != 'closed')
                <div class="card-footer bg-white border-top">
                    <form action="{{ route('admin.feedbacks.reply', $feedback) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="reply_message" class="form-control" 
                               placeholder="Type your reply here..." required style="border-radius: 20px;">
                        <button type="submit" class="btn btn-green" style="border-radius: 20px; white-space: nowrap;">
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
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-tag me-2"></i>Status
            </div>
            <div class="card-body">
                <form action="{{ route('admin.feedbacks.update-status', $feedback) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" {{ $feedback->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="replied" {{ $feedback->status == 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="closed" {{ $feedback->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-green w-100">
                        <i class="fas fa-save me-2"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-custom mt-3">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>User Info
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $feedback->name }}</p>
                <p><strong>Email:</strong> <a href="mailto:{{ $feedback->email }}">{{ $feedback->email }}</a></p>
                <p><strong>Submitted:</strong> {{ $feedback->created_at->format('d M Y, H:i') }}</p>
                <p><strong>Replies:</strong> {{ $feedback->replies->count() }}</p>
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