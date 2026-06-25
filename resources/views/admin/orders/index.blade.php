@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-truck me-2"></i>Orders Management</h4>
        <span class="text-muted small">Total Orders: {{ $orders->count() }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->user->name ?? 'N/A' }}<br><small class="text-muted">{{ $order->user->email ?? '' }}</small></td>
                            <td>{{ $order->items->count() }}</td>
                            <td>RM {{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->statusColor }}">
                                    {{ $order->statusLabel }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <!-- Update Status Form -->
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="width:130px;">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-green">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-truck fa-2x d-block mb-2"></i>
                                No orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-number">{{ $orders->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Pending</div>
                        <div class="stat-number">{{ $orders->where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock" style="color:#ffc107;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Completed</div>
                        <div class="stat-number">{{ $orders->where('status', 'completed')->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle" style="color:#28a745;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-number">RM {{ number_format($orders->sum('total_amount'), 2) }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ring" style="color:#ffc107;"></i></div>
                </div>
            </div>
        </div>
    </div>
@endsection