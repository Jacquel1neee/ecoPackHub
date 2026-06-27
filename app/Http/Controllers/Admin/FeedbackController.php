<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::orderBy('last_reply_at', 'desc')->get();
        $unreadCount = FeedbackReply::where('sender_type', 'user')
            ->where('is_read_by_admin', false)
            ->count();
        $statusCounts = [
            'pending' => Feedback::where('status', 'pending')->count(),
            'replied' => Feedback::where('status', 'replied')->count(),
            'closed' => Feedback::where('status', 'closed')->count(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'unreadCount', 'statusCounts'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('replies');

        foreach ($feedback->replies->where('sender_type', 'user') as $reply) {
            $reply->is_read_by_admin = true;
            $reply->save();
        }

        return view('admin.feedbacks.show', compact('feedback'));
    }

    public function reply(Request $request, Feedback $feedback)
    {
        if ($feedback->status === 'closed') {
            return redirect()->back()->with('error', 'This feedback is closed.');
        }

        $request->validate([
            'reply_message' => 'required|string|min:1|max:2000',
        ]);

        FeedbackReply::create([
            'feedback_id' => $feedback->id,
            'admin_id' => Auth::id(),
            'reply_message' => $request->reply_message,
            'sender_type' => 'admin',
            'sender_name' => Auth::user()->name,
            'is_read_by_admin' => true,
            'is_read_by_user' => false,
        ]);

        $feedback->status = 'replied';
        $feedback->last_reply_at = now();
        $feedback->is_read = false;
        $feedback->save();

        return redirect()->route('admin.feedbacks.show', $feedback)
            ->with('success', 'Reply sent successfully!');
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:pending,replied,closed',
        ]);

        $feedback->status = $request->status;
        $feedback->save();

        return redirect()->route('admin.feedbacks.show', $feedback)
            ->with('success', 'Status updated!');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->replies()->delete();
        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback deleted!');
    }
}