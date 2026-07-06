@php
    use Illuminate\Support\Str;
@endphp

<div class="dropdown">
    <a class="btn btn-outline-light btn-sm position-relative" href="#" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if(isset(
            $unreadCount) && $unreadCount > 0)
            <span id="nav-notif-badge" class="badge bg-danger rounded-pill" style="position: absolute; top: -6px; right: -6px; font-size: 0.6rem; padding: 2px 6px;">{{ $unreadCount }}</span>
        @else
            <span id="nav-notif-badge" class="badge bg-danger rounded-pill" style="position: absolute; top: -6px; right: -6px; font-size: 0.6rem; padding: 2px 6px; display: none;">0</span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 320px; max-height: 420px; overflow: auto;">
        <li class="d-flex justify-content-between align-items-center px-2">
            <strong>Notifications</strong>
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="btn btn-sm btn-link">Mark all read</button>
            </form>
        </li>
        <li><hr class="dropdown-divider"></li>
        @forelse($notifications as $n)
            @php $data = (array) $n->data; @endphp
            <li class="px-2 py-1">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small text-truncate">{{ $data['title'] ?? ($n->type ?? 'Notification') }}</div>
                        <div class="small text-muted">{{ Str::limit($data['message'] ?? '', 80) }}</div>
                        <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="ms-2">
                        @if($n->type === 'PromoteRequest' && is_null($n->read_at))
                            <form method="POST" action="{{ route('notifications.promote.accept', $n->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('notifications.promote.reject', $n->id) }}" class="d-inline ms-1">
                                @csrf
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        @else
                            @if(is_null($n->read_at))
                                <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Mark</button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Read</span>
                            @endif
                        @endif
                    </div>
                </div>
            </li>
            <li><hr class="dropdown-divider"></li>
        @empty
            <li class="px-3 py-2 text-center text-muted">No notifications</li>
        @endforelse
    </ul>
</div>
