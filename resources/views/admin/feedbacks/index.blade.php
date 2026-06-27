@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-comment me-2"></i>Feedbacks</h4>
    <span class="text-muted small">Total: {{ $feedbacks->count() }}</span>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Replies</th>
                        <th>Date</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->id }}</td>
                            <td><strong>{{ $feedback->subject }}</strong></td>
                            <td>{{ $feedback->name }}<br><small class="text-muted">{{ $feedback->email }}</small></td>
                            <td><span class="badge bg-{{ $feedback->statusColor }}">{{ $feedback->statusLabel }}</span></td>
                            <td>{{ $feedback->replies->count() }}</td>
                            <td>{{ $feedback->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.feedbacks.show', $feedback) }}" class="btn btn-sm btn-outline-primary" style="padding: 2px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 8px; font-size: 12px;" onclick="return confirm('Delete this feedback?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-comment fa-2x d-block mb-2"></i>
                                No feedbacks yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection