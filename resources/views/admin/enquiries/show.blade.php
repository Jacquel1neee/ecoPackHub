@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-comments me-2"></i>Enquiry #{{ $enquiry->id }}</h4>
    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <!-- Chat Area -->
    <div class="col-lg-8">
        <!-- Enquiry Info -->
        <div class="card card-custom mb-3">
            <div class="card-header">
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
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-comment-dots me-2"></i>Conversation</span>
                <span class="badge bg-{{ $enquiry->statusColor }}">{{ $enquiry->statusLabel }}</span>
            </div>
            <div class="card-body" id="chat-messages" style="max-height: 450px; overflow-y: auto; background: #f8f9fa;">
                @if($enquiry->replies->count() > 0)
                    @foreach($enquiry->replies as $reply)
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
                                            {{ $reply->sender_name ?? $enquiry->contact_person }}
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

            <!-- ===== REPLY FORM (Admin) ===== -->
            @if($enquiry->status != 'closed')
                <div class="card-footer bg-white border-top">
                    <form action="{{ route('admin.enquiries.reply', $enquiry) }}" method="POST" class="d-flex gap-2">
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
        <!-- Status Update -->
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-tag me-2"></i>Status
            </div>
            <div class="card-body">
                <form action="{{ route('admin.enquiries.update-status', $enquiry) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" {{ $enquiry->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="replied" {{ $enquiry->status == 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="closed" {{ $enquiry->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-green w-100">
                        <i class="fas fa-save me-2"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card card-custom mt-3">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>Customer
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $enquiry->contact_person }}</p>
                <p><strong>Phone:</strong> <a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a></p>
                <p><strong>Email:</strong> <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></p>
                <p><strong>Submitted:</strong> {{ $enquiry->created_at->format('d M Y, H:i') }}</p>
                <p><strong>Total Replies:</strong> {{ $enquiry->replies->count() }}</p>
            </div>
        </div>

        <!-- Product -->
        @if($enquiry->product)
            <div class="card card-custom mt-3">
                <div class="card-header">
                    <i class="fas fa-box me-2"></i>Product
                </div>
                <div class="card-body text-center">
                    @if($enquiry->product->image_path)
                        <img src="{{ asset($enquiry->product->image_path) }}" class="img-fluid rounded" style="max-height: 100px;">
                    @endif
                    <p class="mt-2"><strong>{{ $enquiry->product_name }}</strong></p>
                    <a href="{{ route('admin.products.show', $enquiry->product) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View Product
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Auto-scroll to bottom -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection