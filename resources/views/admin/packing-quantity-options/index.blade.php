@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-list-ul me-2"></i>Option</h4>
    <a href="{{ route('admin.packing-quantity-options.create') }}" class="btn btn-green">
        <i class="fas fa-plus me-2"></i>Add Option
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($options as $option)
                        <tr>
                            <td>{{ $options->firstItem() + $loop->index }}</td>
                            <td>{{ $option->name }}</td>
                            <td>
                                @if($option->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.packing-quantity-options.edit', $option) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.packing-quantity-options.destroy', $option) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this option?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No options found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $options->links() }}
    </div>
</div>
@endsection
