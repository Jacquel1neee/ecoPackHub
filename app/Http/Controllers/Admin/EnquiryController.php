<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with('product')->orderBy('last_reply_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $enquiries = $query->get();
        $statusCounts = [
            'pending' => Enquiry::where('status', 'pending')->count(),
            'replied' => Enquiry::where('status', 'replied')->count(),
            'closed' => Enquiry::where('status', 'closed')->count(),
        ];

        $unreadCount = EnquiryReply::where('sender_type', 'user')
            ->where('is_read_by_admin', false)
            ->count();

        return view('admin.enquiries.index', compact('enquiries', 'statusCounts', 'unreadCount'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load('replies', 'product');
        
        // Mark all user replies as read when admin views
        foreach ($enquiry->replies->where('sender_type', 'user') as $reply) {
            $reply->is_read_by_admin = true;
            $reply->save();
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'reply_message' => 'required|string|min:1|max:2000',
        ]);

        if ($enquiry->status === 'closed') {
            return redirect()->back()->with('error', 'This enquiry is closed. Cannot reply.');
        }

        EnquiryReply::create([
            'enquiry_id' => $enquiry->id,
            'admin_id' => Auth::id(),
            'reply_message' => $request->reply_message,
            'sender_type' => 'admin',
            'sender_name' => Auth::user()->name,
            'is_read_by_admin' => true,
            'is_read_by_user' => false,
        ]);

        $enquiry->status = 'replied';
        $enquiry->last_reply_at = now();
        $enquiry->is_read = false; // mark as unread so the user will receive a notification
        $enquiry->save();

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('success', 'Reply sent successfully!');
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,replied,closed',
        ]);

        $enquiry->status = $request->status;
        $enquiry->save();

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('success', 'Status updated to ' . $enquiry->status_label . '!');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->replies()->delete();
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Enquiry deleted successfully!');
    }
}