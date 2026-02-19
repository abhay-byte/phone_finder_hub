<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show()
    {
        return view('profile', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Basic profanity list (expand as needed or move to config)
        $badWords = [
            'admin', 'root', 'superuser', 'moderator',
            'fuck', 'shit', 'piss', 'cunt', 'bitch', 'asshole', 'dick', 'cock', 
            'pussy', 'whore', 'slut', 'fag', 'nigger', 'bastard', 'damn',
            'sex', 'xxx', 'porn', 'anal', 'tit', 'boob'
        ];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('users')->ignore($user->id),
                function ($attribute, $value, $fail) use ($badWords) {
                    $lower = strtolower($value);
                    foreach ($badWords as $word) {
                        if (str_contains($lower, $word)) {
                            $fail('The username contains inappropriate language or reserved words.');
                            return;
                        }
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8', 'max:1024'],
        ]);

        // Update basic info
        $user->name = strip_tags($request->name);
        $user->username = strip_tags($request->username);
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
