@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-plus me-2"></i>Add New Category</h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon (Font Awesome class)</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', 'fa-solid fa-box') }}" placeholder="e.g., fa-solid fa-bowl-food">
                    <small class="text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> for icons</small>
                </div>
                <button type="submit" class="btn btn-green">
                    <i class="fas fa-save"></i> Save Category
                </button>
            </form>
        </div>
    </div>
@endsection