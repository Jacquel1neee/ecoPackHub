<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PromoteRequest;
use App\Models\UnlinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HierarchyController extends Controller
{
    /**
     * Display hierarchy tree for current admin
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 1) {
            abort(403);
        }

        $downlines = $user->getDownlinesTree();

        $promotableUsers = $this->getPromotableUsers($user);

        $pendingPromotes = PromoteRequest::where('target_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $pendingUnlinks = UnlinkRequest::where('target_id', $user->id)
            ->where('status', 'pending')
            ->get();

        return view('admin.hierarchy.index', compact(
            'user',
            'downlines',
            'promotableUsers',
            'pendingPromotes',
            'pendingUnlinks'
        ));
    }

    /**
     * Get users that current user can promote
     */
    private function getPromotableUsers($user)
    {
        return User::where('id', '!=', $user->id)
            ->where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('role', 1)
                        ->where('level', '<', $user->level)
                        ->whereNull('promoted_by');
                })->orWhere(function ($q) use ($user) {
                    $q->where('role', 0)
                        ->where('level', '<', $user->level)
                        ->whereNull('promoted_by');
                });
            })
            ->get();
    }

    /**
     * Send promote request to target user
     */
    public function sendPromoteRequest(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $promoter */
        $promoter = Auth::user();
        /** @var \App\Models\User $target */
        $target = User::find($request->target_id);

        if (!$promoter->canPromote($target)) {
            return redirect()->back()->with('error', 'You cannot promote this user.');
        }

        $existing = PromoteRequest::where('promoter_id', $promoter->id)
            ->where('target_id', $target->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have a pending request for this user.');
        }

        // Create promote request
        PromoteRequest::create([
            'promoter_id' => $promoter->id,
            'target_id' => $target->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Create an in-app notification for the target so they can accept/reject from bell
        $promoteReq = PromoteRequest::where('promoter_id', $promoter->id)
            ->where('target_id', $target->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($promoteReq) {
            \App\Models\UserNotification::create([
                'type' => 'PromoteRequest',
                'notifiable_type' => get_class($target),
                'notifiable_id' => $target->id,
                'data' => [
                    'title' => 'Promotion Invitation',
                    'message' => "{$promoter->name} invited you to be their downline and become an admin.",
                    'promote_request_id' => $promoteReq->id,
                    'promoter_id' => $promoter->id,
                    'promoter_name' => $promoter->name,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Promote request sent to {$target->name}!");
    }

    /**
     * Accept promote request
     */
    public function acceptPromoteRequest(PromoteRequest $request)
    {
        if ($request->target_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is no longer pending.');
        }

        /** @var \App\Models\User $promoter */
        $promoter = User::find($request->promoter_id);
        /** @var \App\Models\User $target */
        $target = Auth::user();

        if (!$promoter->canPromote($target)) {
            $request->status = 'rejected';
            $request->responded_at = now();
            $request->save();

            return redirect()->back()->with('error', 'You can no longer be promoted by this user.');
        }

        // Update user relationship
        $target->promoted_by = $promoter->id;

        if ($target->role === 0) {
            $target->role = 1;
            $target->level = 1;
        } else {
            $target->level = $target->calculateLevel();
        }

        $target->updatePath($promoter->id);

        $target->save();

        // Update request status
        $request->status = 'accepted';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', "You are now under {$promoter->name}!");
    }

    /**
     * Reject promote request
     */
    public function rejectPromoteRequest(PromoteRequest $request)
    {
        if ($request->target_id !== Auth::id()) {
            abort(403);
        }

        $request->status = 'rejected';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', 'Promote request rejected.');
    }

    /**
     * Cancel promote request (by promoter)
     */
    public function cancelPromoteRequest(PromoteRequest $request)
    {
        if ($request->promoter_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is no longer pending.');
        }

        $request->status = 'cancelled';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', 'Promote request cancelled.');
    }

    /**
     * Send unlink request to promoter
     */
    public function sendUnlinkRequest(Request $request)
    {
        /** @var \App\Models\User $requester */
        $requester = Auth::user();

        if (!$requester->canUnlink()) {
            return redirect()->back()->with('error', 'You do not have an upline to unlink from.');
        }

        $target = $requester->promoter;

        $existing = UnlinkRequest::where('requester_id', $requester->id)
            ->where('target_id', $target->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have a pending unlink request.');
        }

        UnlinkRequest::create([
            'requester_id' => $requester->id,
            'target_id' => $target->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', "Unlink request sent to {$target->name}!");
    }

    /**
     * Accept unlink request
     */
    public function acceptUnlinkRequest(UnlinkRequest $request)
    {
        if ($request->target_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is no longer pending.');
        }

        /** @var \App\Models\User $requester */
        $requester = User::find($request->requester_id);
        /** @var \App\Models\User $promoter */
        $promoter = Auth::user();

        // Remove relationship
        $requester->promoted_by = null;
        $requester->path = null;
        $requester->last_sales_check = null;
        $requester->save();

        // Recalculate all downlines' paths since the requester is now independent
        // Paths are rooted at the requester, so update all children paths
        foreach ($requester->downlines as $downline) {
            $downline->updatePath($requester->id);
        }

        $request->status = 'accepted';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', "Unlink request accepted. {$requester->name} is now independent.");
    }

    /**
     * Reject unlink request
     */
    public function rejectUnlinkRequest(UnlinkRequest $request)
    {
        if ($request->target_id !== Auth::id()) {
            abort(403);
        }

        $request->status = 'rejected';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', 'Unlink request rejected.');
    }

    /**
     * Cancel unlink request (by requester)
     */
    public function cancelUnlinkRequest(UnlinkRequest $request)
    {
        if ($request->requester_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is no longer pending.');
        }

        $request->status = 'cancelled';
        $request->responded_at = now();
        $request->save();

        return redirect()->back()->with('success', 'Unlink request cancelled.');
    }
}