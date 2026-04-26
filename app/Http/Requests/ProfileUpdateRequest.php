<?php

namespace App\Http\Requests;

use App\Repositories\UserRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->getAuthIdentifier();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($userId) {
                    $existing = app(UserRepository::class)->findByEmail($value);
                    if ($existing && $existing->id !== $userId) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
        ];
    }
}
