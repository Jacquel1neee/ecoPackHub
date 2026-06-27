<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
            'last_reply_at' => now(),
        ]);

        return redirect()->route('feedback.show', $feedback)
            ->with('success', 'Feedback sent successfully! We will get back to you soon.');
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->user_id !== null) {
            if (Auth::id() !== $feedback->user_id && (!Auth::check() || Auth::user()->role !== 1)) {
                abort(403);
            }
        } else {
            if (Auth::check() && Auth::user()->role !== 1) {
                abort(403);
            }
        }

        $feedback->load('replies');
        return view('feedback.show', compact('feedback'));
    }

    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $feedbacks = Feedback::where('user_id', Auth::id())
            ->orderBy('last_reply_at', 'desc')
            ->get();

        return view('feedback.history', compact('feedbacks'));
    }

    public function userReply(Request $request, Feedback $feedback)
    {
        if ($feedback->user_id !== null) {
            if (Auth::id() !== $feedback->user_id) {
                abort(403);
            }
        } elseif (Auth::check()) {
            abort(403);
        }

        if ($feedback->status === 'closed') {
            return redirect()->back()->with('error', 'This feedback is closed.');
        }

        $request->validate([
            'reply_message' => 'required|string|min:1|max:2000',
        ]);

        FeedbackReply::create([
            'feedback_id' => $feedback->id,
            'admin_id' => null,
            'reply_message' => $request->reply_message,
            'sender_type' => 'user',
            'sender_name' => Auth::check() ? Auth::user()->name : $feedback->name,
            'is_read_by_admin' => false,
            'is_read_by_user' => true,
        ]);

        $feedback->status = 'pending';
        $feedback->last_reply_at = now();
        $feedback->save();

        return redirect()->route('feedback.show', $feedback)
            ->with('success', 'Reply sent!');
    }
}