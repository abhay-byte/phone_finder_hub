<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users for super admins to manage.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->latest()->paginate(25)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Update the specified user's role.
     */
    public function updateRole(Request $request, User $user)
    {
        // Only allow changing roles to valid enums. 
        // We shouldn't allow the currently logged in super admin to downgrade themselves accidentally.
        $validated = $request->validate([
            'role' => 'required|string|in:user,author,super_admin',
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'super_admin') {
            return redirect()->back()->with('error', 'You cannot change your own super admin role.');
        }

        $user->update(['role' => $validated['role']]);

        return redirect()->back()->with('success', "User role updated successfully to {$validated['role']}.");
    }
}
