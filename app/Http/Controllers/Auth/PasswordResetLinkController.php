<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Firestore\FirestoreClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    protected UserRepository $users;

    protected FirestoreClient $firestore;

    public function __construct(UserRepository $users, FirestoreClient $firestore)
    {
        $this->users = $users;
        $this->firestore = $firestore;
    }

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = $this->users->findByEmail($request->email);

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $token = Str::random(64);

        $this->firestore->setDocument('password_reset_tokens', $token, [
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->format('c'),
        ]);

        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

        Mail::raw("Click here to reset your password: {$resetUrl}", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Password Notification');
        });

        return back()->with('status', 'We have emailed your password reset link!');
    }
}
