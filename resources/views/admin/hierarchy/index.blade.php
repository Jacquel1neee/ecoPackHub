@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-sitemap me-2"></i>Hierarchy</h4>
    <div>
        <span class="badge bg-primary me-2">
            <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
        </span>
        <span class="badge" style="background-color: {{ Auth::user()->levelColor }}; color: #fff;">
            {{ Auth::user()->levelEmoji }} {{ Auth::user()->levelName }}
        </span>
        <span class="badge bg-success">
            <i class="fas fa-ring me-1"></i> RM {{ number_format(Auth::user()->group_sales, 2) }}
        </span>
    </div>
</div>

<!-- Pending Requests Alerts -->
@if($pendingPromotes->count() > 0)
    <div class="alert alert-info alert-dismissible fade show">
        <strong><i class="fas fa-bell me-2"></i>Pending Promote Requests</strong>
        <ul class="mb-0 mt-2">
            @foreach($pendingPromotes as $request)
                <li class="d-flex justify-content-between align-items-center">
                    <span>{{ $request->promoter->name }} wants to promote you!</span>
                    <div>
                        <form action="{{ route('admin.hierarchy.accept-promote', $request) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Accept</button>
                        </form>
                        <form action="{{ route('admin.hierarchy.reject-promote', $request) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if($pendingUnlinks->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show">
        <strong><i class="fas fa-handshake me-2"></i>Pending Unlink Requests</strong>
        <ul class="mb-0 mt-2">
            @foreach($pendingUnlinks as $request)
                <li class="d-flex justify-content-between align-items-center">
                    <span>{{ $request->requester->name }} wants to unlink from you!</span>
                    <div>
                        <form action="{{ route('admin.hierarchy.accept-unlink', $request) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Accept</button>
                        </form>
                        <form action="{{ route('admin.hierarchy.reject-unlink', $request) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- Tree View -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-tree me-2"></i>Your Downline Tree
            </div>
            <div class="card-body">
                @if(count($downlines) > 0)
                    <div class="tree-container">
                        <ul class="tree">
                            <li>
                                <div class="tree-node current-user">
                                    <span class="level-badge" style="background-color: {{ Auth::user()->levelColor }};">{{ Auth::user()->level }}</span>
                                    <span class="user-name">{{ Auth::user()->name }}</span>
                                    <span class="user-level">{{ Auth::user()->levelName }}</span>
                                    <span class="user-sales">RM {{ number_format(Auth::user()->group_sales, 2) }}</span>
                                </div>
                                @if(count($downlines) > 0)
                                    <ul>
                                        @foreach($downlines as $downline)
                                            @include('admin.hierarchy.partials.tree-node', ['node' => $downline])
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x d-block mb-3"></i>
                        <p>You don't have any downlines yet.</p>
                        <p class="small">Promote other users to build your team!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="col-lg-4">
        <!-- Promote User -->
        <div class="card card-custom mb-3">
            <div class="card-header">
                <i class="fas fa-user-plus me-2"></i>Promote User
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hierarchy.send-promote') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select User to Promote</label>
                        <select name="target_id" class="form-select" required>
                            <option value="">-- Select User --</option>
                            @foreach($promotableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} 
                                    ({{ $user->levelName }}) 
                                    @if($user->role == 0) - Member @endif
                                </option>
                            @endforeach
                        </select>
                        @if($promotableUsers->count() == 0)
                            <small class="text-muted">No users available to promote.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Invitation message..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-green w-100" {{ $promotableUsers->count() == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane me-2"></i> Send Promote Request
                    </button>
                </form>
            </div>
        </div>

        <!-- Unlink from Upline -->
        @if(Auth::user()->promoted_by)
            <div class="card card-custom">
                <div class="card-header">
                    <i class="fas fa-unlink me-2"></i>Unlink from Upline
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Your current upline: <strong>{{ Auth::user()->promoter->name ?? 'Unknown' }}</strong>
                    </p>
                    <form action="{{ route('admin.hierarchy.send-unlink') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Reason (Optional)</label>
                            <textarea name="message" class="form-control" rows="2" placeholder="Why do you want to unlink?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-unlink me-2"></i> Request Unlink
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .tree,
    .tree ul {
        list-style: none;
        padding-left: 20px;
        margin: 0;
    }

    .tree li {
        margin: 5px 0;
        position: relative;
    }

    .tree li::before {
        content: '';
        position: absolute;
        left: -15px;
        top: 0;
        bottom: 0;
        border-left: 2px solid #ddd;
    }

    .tree li::after {
        content: '';
        position: absolute;
        left: -15px;
        top: 50%;
        width: 15px;
        border-top: 2px solid #ddd;
    }

    .tree li:last-child::before {
        height: 50%;
    }

    .tree-node {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 8px;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        font-size: 14px;
        transition: all 0.2s;
    }

    .tree-node:hover {
        background: #e8f5e9;
        border-color: var(--primary-green);
    }

    .tree-node.current-user {
        background: #e8f5e9;
        border-color: var(--primary-green);
        border-width: 2px;
    }

    .tree-node .level-badge {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        color: #fff;
        text-align: center;
        line-height: 24px;
        font-size: 12px;
        font-weight: bold;
    }

    .tree-node .user-name {
        font-weight: 600;
    }

    .tree-node .user-level {
        font-size: 12px;
        color: #666;
        background: #f0f0f0;
        padding: 1px 8px;
        border-radius: 12px;
    }

    .tree-node .user-sales {
        font-size: 12px;
        color: var(--primary-green);
        font-weight: 500;
    }
</style>
@endsection