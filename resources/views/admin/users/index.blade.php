@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-users me-2"></i>User Management</h4>
        <span class="text-muted small">Total Users: {{ $users->count() }}</span>
    </div>

    <div class="card card-custom">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-primary ms-1">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 1)
                                    <span class="badge bg-success">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user me-1"></i> Member
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <!-- Toggle Role Button -->
                                @if($user->id !== auth()->id())
                                    @php
                                        $toggleRoleLabel = $user->role === 1 ? 'Member' : 'Admin';
                                    @endphp
                                    <form action="{{ route('admin.users.toggle-role', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->role === 1 ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                onclick="return confirm('Are you sure you want to change {{ $user->name }} role to {{ $toggleRoleLabel }}?')">
                                            <i class="fas {{ $user->role === 1 ? 'fa-user' : 'fa-user-shield' }}"></i>
                                            {{ $user->role === 1 ? 'Demote to Member' : 'Promote to Admin' }}
                                        </button>
                                    </form>

                                    <!-- Delete User Button -->
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">(Current user)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional: Quick Stats -->
    <div class="row g-3 mt-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-number">{{ $users->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Admins</div>
                        <div class="stat-number">{{ $users->where('role', 1)->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Members</div>
                        <div class="stat-number">{{ $users->where('role', 0)->count() }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-user"></i></div>
                </div>
            </div>
        </div>
    </div>
@endsection