<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Firestore\FirestoreClient;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    protected UserRepository $users;

    protected FirestoreClient $firestore;

    public function __construct(UserRepository $users, FirestoreClient $firestore)
    {
        $this->users = $users;
        $this->firestore = $firestore;
    }

    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tokenDoc = $this->firestore->getDocument('password_reset_tokens', $request->token);

        if (! $tokenDoc || $tokenDoc['email'] !== $request->email) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token is invalid.']);
        }

        $createdAt = isset($tokenDoc['created_at']) ? new \DateTime($tokenDoc['created_at']) : null;
        $now = new \DateTime;
        if (! $createdAt || ($now->getTimestamp() - $createdAt->getTimestamp()) > 3600) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token has expired.']);
        }

        $user = $this->users->findByEmail($request->email);

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $this->users->update($user->id, [
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        $this->firestore->deleteDocument('password_reset_tokens', $request->token);

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
