<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  LOGIN
    // ─────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Rate-limit: max 5 attempts per minute per IP+identifier combo
        $throttleKey = Str::lower($request->input('identifier', '')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts('login:' . $throttleKey, 5)) {
            $seconds = RateLimiter::availableIn('login:' . $throttleKey);
            throw ValidationException::withMessages([
                'identifier' => __("Too many login attempts. Please try again in {$seconds} second(s)."),
            ]);
        }

        $request->validate([
            'identifier' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:1024',
            ],
        ], [
            'identifier.required' => 'Username or email is required.',
            'password.required'   => 'Password is required.',
            'password.min'        => 'Password must be at least 8 characters.',
        ]);

        $identifier = strip_tags(trim($request->input('identifier')));
        $password   = $request->input('password');

        // Determine if identifier is an email or a username
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field     => $identifier,
            'password' => $password,
        ];

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear('login:' . $throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit('login:' . $throttleKey, 60);

        throw ValidationException::withMessages([
            'identifier' => 'These credentials do not match our records.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  REGISTER
    // ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Rate-limit registrations: 3 per 10 minutes per IP
        $throttleKey = 'register|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many registration attempts. Please try again in {$seconds} second(s).",
            ]);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:80',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:users,username',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'name.regex'        => 'Name may only contain letters, spaces, and hyphens.',
            'username.regex'    => 'Username may only contain letters, numbers, underscores, and hyphens.',
            'username.unique'   => 'This username is already taken.',
            'email.unique'      => 'This email address is already registered.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        RateLimiter::hit($throttleKey, 600);

        $user = User::create([
            'name'     => strip_tags(trim($request->name)),
            'username' => strtolower(trim($request->username)),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        RateLimiter::clear($throttleKey);

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Account created! Welcome to PhoneFinderHub, ' . $user->name . '!');
    }

    // ─────────────────────────────────────────────────────────────
    //  LOGOUT
    // ─────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                ->with('success', 'You have been securely logged out.');
        }

        return redirect()->route('home');
    }
}
