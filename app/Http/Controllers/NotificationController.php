<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = UserNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = $notifications->whereNull('read_at')->count();

        return view('partials.notifications_dropdown', compact('notifications', 'unreadCount'));
    }

    public function markRead($id)
    {
        $user = Auth::user();
        $notif = UserNotification::where('id', $id)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        $notif->markRead();

        return back();
    }

    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        UserNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    /**
     * Accept a promote request from the notification dropdown
     */
    public function promoteAccept($notificationId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notif = UserNotification::where('id', $notificationId)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        $data = $notif->data ?? [];
        $reqId = $data['promote_request_id'] ?? null;

        if (!$reqId) {
            return back()->with('error', 'Invalid promote notification.');
        }

        $promoteReq = \App\Models\PromoteRequest::find($reqId);
        if (!$promoteReq || $promoteReq->status !== 'pending' || $promoteReq->target_id !== $user->id) {
            return back()->with('error', 'This promote request cannot be accepted.');
        }

        $promoter = \App\Models\User::find($promoteReq->promoter_id);

        // perform same accept logic as HierarchyController::acceptPromoteRequest
        $user->promoted_by = $promoter->id;
        if ($user->role === 0) {
            $user->role = 1;
            $user->level = 1;
        } else {
            $user->level = $user->calculateLevel();
        }

        $user->updatePath($promoter->id);
        $user->save();

        $promoteReq->status = 'accepted';
        $promoteReq->responded_at = now();
        $promoteReq->save();

        // mark notification read
        $notif->markRead();

        // notify promoter optionally
        \App\Models\UserNotification::create([
            'type' => 'PromoteAccepted',
            'notifiable_type' => get_class($promoter),
            'notifiable_id' => $promoter->id,
            'data' => [
                'title' => 'Promotion Accepted',
                'message' => "{$user->name} accepted your promotion invitation.",
                'target_id' => $user->id,
            ],
        ]);

        return back()->with('success', 'You are now under ' . $promoter->name);
    }

    /**
     * Reject a promote request from the notification dropdown
     */
    public function promoteReject($notificationId)
    {
        $user = Auth::user();

        $notif = UserNotification::where('id', $notificationId)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        $data = $notif->data ?? [];
        $reqId = $data['promote_request_id'] ?? null;

        if (!$reqId) {
            return back()->with('error', 'Invalid promote notification.');
        }

        $promoteReq = \App\Models\PromoteRequest::find($reqId);
        if (!$promoteReq || $promoteReq->status !== 'pending' || $promoteReq->target_id !== $user->id) {
            return back()->with('error', 'This promote request cannot be rejected.');
        }

        $promoteReq->status = 'rejected';
        $promoteReq->responded_at = now();
        $promoteReq->save();

        $notif->markRead();

        // notify promoter optionally
        $promoter = \App\Models\User::find($promoteReq->promoter_id);
        if ($promoter) {
            \App\Models\UserNotification::create([
                'type' => 'PromoteRejected',
                'notifiable_type' => get_class($promoter),
                'notifiable_id' => $promoter->id,
                'data' => [
                    'title' => 'Promotion Rejected',
                    'message' => "{$user->name} rejected your promotion invitation.",
                    'target_id' => $user->id,
                ],
            ]);
        }

        return back()->with('success', 'Promotion request rejected.');
    }
}
