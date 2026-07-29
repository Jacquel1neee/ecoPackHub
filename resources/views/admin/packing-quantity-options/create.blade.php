@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-plus me-2"></i>Add Option</h4>
    <a href="{{ route('admin.packing-quantity-options.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-custom">
    <div class="card-body">
        <form action="{{ route('admin.packing-quantity-options.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Option Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Example: pcs/ctn, pcs/pkt, sets/ctn</small>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <button type="submit" class="btn btn-green">
                <i class="fas fa-save me-2"></i>Save Option
            </button>
        </form>
    </div>
</div>
@endsection
