<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\EnquiryReply;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    // Show enquiry form for a product
    public function create(Product $product)
    {
        return view('enquiry.create', compact('product'));
    }

    // Store enquiry
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'quantity' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        $product = Product::find($request->product_id);

        $enquiry = Enquiry::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'product_id' => $product->id,
            'product_code' => $product->code,
            'product_name' => $product->name,
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'email' => $request->email,
            'quantity' => $request->quantity,
            'message' => $request->message,
            'status' => 'pending',
            'last_reply_at' => now(),
        ]);

        return redirect()->route('enquiry.show', $enquiry)
            ->with('success', 'Your enquiry has been submitted successfully!');
    }

    // Success page (redirect to show instead)
    public function success()
    {
        return view('enquiry.success');
    }

    // User's enquiry history
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $enquiries = Enquiry::where('user_id', Auth::id())
            ->orderBy('last_reply_at', 'desc')
            ->get();

        // Mark all as read when user views list
        foreach ($enquiries as $enquiry) {
            if ($enquiry->is_read == false) {
                $enquiry->is_read = true;
                $enquiry->save();
            }
        }

        return view('enquiry.history', compact('enquiries'));
    }

    // View single enquiry (for users)
    public function show(Enquiry $enquiry)
    {
        if ($enquiry->user_id !== Auth::id() && Auth::user()->role !== 1) {
            abort(403);
        }

        $enquiry->load('replies');
        
        // Mark user replies as read by admin when admin views
        if (Auth::user()->role === 1) {
            foreach ($enquiry->replies->where('sender_type', 'user') as $reply) {
                $reply->is_read_by_admin = true;
                $reply->save();
            }
        }
        
        // Mark admin replies as read by user when user views
        if (Auth::user()->role !== 1) {
            foreach ($enquiry->replies->where('sender_type', 'admin') as $reply) {
                $reply->is_read_by_user = true;
                $reply->save();
            }
        }

        return view('enquiry.show', compact('enquiry'));
    }

    // ===== NEW: User can reply to enquiry (like chat) =====
    public function userReply(Request $request, Enquiry $enquiry)
    {
        if ($enquiry->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reply_message' => 'required|string|min:1|max:2000',
        ]);

        if ($enquiry->status === 'closed') {
            return redirect()->back()->with('error', 'This enquiry is closed. Cannot reply.');
        }

        $reply = EnquiryReply::create([
            'enquiry_id' => $enquiry->id,
            'admin_id' => null,
            'reply_message' => $request->reply_message,
            'sender_type' => 'user',
            'sender_name' => Auth::user()->name,
            'is_read_by_admin' => false,
            'is_read_by_user' => true,
        ]);

        $enquiry->status = 'pending';
        $enquiry->last_reply_at = now();
        $enquiry->save();

        return redirect()->route('enquiry.show', $enquiry)
            ->with('success', 'Reply sent! Admin will get back to you.');
    }
}