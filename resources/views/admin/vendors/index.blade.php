@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-truck me-2"></i>Vendors
        </h2>
        <a href="{{ route('admin.vendors.create') }}" class="btn" style="background-color: var(--primary-green); color: #fff;">
            <i class="fas fa-plus me-2"></i>Add Vendor
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td>{{ $vendors->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $vendor->name }}</strong></td>
                                <td>{{ $vendor->contact_person ?? 'N/A' }}</td>
                                <td>{{ $vendor->email ?? 'N/A' }}</td>
                                <td>{{ $vendor->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($vendor->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $vendor->products->count() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.vendors.edit', $vendor) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vendors.destroy', $vendor) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this vendor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection