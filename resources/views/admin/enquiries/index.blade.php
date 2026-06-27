@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-envelope me-2"></i>Enquiries</h4>
    <span class="text-muted small">Total: {{ $enquiries->count() }}</span>
</div>

<!-- Status Filter -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm {{ !request()->has('status') ? 'btn-green' : 'btn-outline-secondary' }}">
        All ({{ $statusCounts['pending'] + $statusCounts['replied'] + $statusCounts['closed'] }})
    </a>
    <a href="{{ route('admin.enquiries.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request()->get('status') == 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
        Pending ({{ $statusCounts['pending'] }})
    </a>
    <a href="{{ route('admin.enquiries.index', ['status' => 'replied']) }}" class="btn btn-sm {{ request()->get('status') == 'replied' ? 'btn-info' : 'btn-outline-secondary' }}">
        Replied ({{ $statusCounts['replied'] }})
    </a>
    <a href="{{ route('admin.enquiries.index', ['status' => 'closed']) }}" class="btn btn-sm {{ request()->get('status') == 'closed' ? 'btn-success' : 'btn-outline-secondary' }}">
        Closed ({{ $statusCounts['closed'] }})
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Contact</th>
                        <th>Phone/Email</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $enquiry)
                        <tr>
                            <td>{{ $enquiry->id }}</td>
                            <td>
                                <strong>{{ $enquiry->product_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $enquiry->product_code }}</small>
                            </td>
                            <td>{{ $enquiry->contact_person }}</td>
                            <td>
                                <small>{{ $enquiry->phone }}</small>
                                <br>
                                <small>{{ $enquiry->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $enquiry->statusColor }}">
                                    {{ $enquiry->statusLabel }}
                                </span>
                            </td>
                            <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1" style="flex-wrap: nowrap;">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary" style="padding: 2px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 8px; font-size: 12px;" onclick="return confirm('Delete this enquiry?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-envelope fa-2x d-block mb-2"></i>
                                No enquiries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection