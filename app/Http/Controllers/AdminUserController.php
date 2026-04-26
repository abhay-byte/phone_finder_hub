<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminUserController extends Controller
{
    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $all = $this->users->all();

        if ($search) {
            $lower = strtolower($search);
            $all = array_filter($all, function ($user) use ($lower) {
                return str_contains(strtolower($user->name), $lower)
                    || str_contains(strtolower($user->username ?? ''), $lower)
                    || str_contains(strtolower($user->email), $lower);
            });
        }

        usort($all, function ($a, $b) {
            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });

        $page = (int) $request->input('page', 1);
        $perPage = 25;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $users = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, string $userId)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:user,author,super_admin',
        ]);

        $user = $this->users->findOrFail($userId);

        if ($user->id === auth()->id() && $validated['role'] !== 'super_admin') {
            return redirect()->back()->with('error', 'You cannot change your own super admin role.');
        }

        $this->users->update($user->id, ['role' => $validated['role']]);

        return redirect()->back()->with('success', "User role updated successfully to {$validated['role']}.");
    }
}
