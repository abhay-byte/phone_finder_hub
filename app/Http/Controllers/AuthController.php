<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Services\FirebaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected FirebaseAuthService $firebaseAuth;

    protected UserRepository $users;

    public function __construct(FirebaseAuthService $firebaseAuth, UserRepository $users)
    {
        $this->firebaseAuth = $firebaseAuth;
        $this->users = $users;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $throttleKey = Str::lower($request->input('identifier', '')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts('login:'.$throttleKey, 5)) {
            $seconds = RateLimiter::availableIn('login:'.$throttleKey);
            throw ValidationException::withMessages([
                'identifier' => __("Too many login attempts. Please try again in {$seconds} second(s)."),
            ]);
        }

        $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:1024'],
        ], [
            'identifier.required' => 'Username or email is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $identifier = strip_tags(trim($request->input('identifier')));
        $password = $request->input('password');

        if (! filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = app(\App\Repositories\UserRepository::class)->findByUsername($identifier);
            if ($user) {
                $identifier = $user->email;
            }
        }

        $credentials = [
            'email' => $identifier,
            'password' => $password,
        ];

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear('login:'.$throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back, '.Auth::user()->name.'!');
        }

        RateLimiter::hit('login:'.$throttleKey, 60);

        throw ValidationException::withMessages([
            'identifier' => 'These credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $throttleKey = 'register|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many registration attempts. Please try again in {$seconds} second(s).",
            ]);
        }

        $isTesting = app()->environment('testing');

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\s\-]+$/u'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_\-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'name.regex' => 'Name may only contain letters, spaces, and hyphens.',
            'username.regex' => 'Username may only contain letters, numbers, underscores, and hyphens.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $username = strtolower(trim($request->username));
        $email = strtolower(trim($request->email));

        if ($this->users->findByUsername($username)) {
            throw ValidationException::withMessages([
                'username' => 'This username is already taken.',
            ]);
        }

        if ($this->users->findByEmail($email)) {
            throw ValidationException::withMessages([
                'email' => 'This email address is already registered.',
            ]);
        }

        RateLimiter::hit($throttleKey, 600);

        $user = $this->users->create([
            'name' => strip_tags(trim($request->name)),
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        RateLimiter::clear($throttleKey);

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Account created! Welcome to PhoneFinderHub, '.$user->name.'!');
    }

    public function firebaseCallback(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $verifiedToken = $this->firebaseAuth->verifyIdToken($request->id_token);

        if (! $verifiedToken) {
            return response()->json(['message' => 'Invalid Firebase token'], 401);
        }

        $firebaseUser = $this->firebaseAuth->getUser($verifiedToken->claims()->get('sub'));

        if (! $firebaseUser) {
            return response()->json(['message' => 'Failed to retrieve user'], 401);
        }

        $user = $this->firebaseAuth->syncUser($firebaseUser);

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'redirect' => route('home'),
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->firebase_uid) {
                $this->firebaseAuth->revokeRefreshTokens($user->firebase_uid);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                ->with('success', 'You have been securely logged out.');
        }

        return redirect()->route('home');
    }
}
