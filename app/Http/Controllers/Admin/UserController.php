<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle user role between 0 (member) and 1 (admin).
     */
    public function toggleRole(User $user)
    {
        // Prevent admin from changing their own role (optional but recommended)
        $authUserId = Auth::id();
        if ($user->getKey() === $authUserId) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot change your own role.');
        }

        // Toggle role: 0 -> 1, 1 -> 0
        $user->role = $user->role === 1 ? 0 : 1;
        $user->save();

        $roleText = $user->role === 1 ? 'Admin' : 'Member';
        
        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' has been promoted to {$roleText}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        $authUserId = Auth::id();
        if ($user->getKey() === $authUserId) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' has been deleted.");
    }
}