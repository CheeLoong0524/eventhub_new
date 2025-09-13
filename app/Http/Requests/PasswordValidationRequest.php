<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters (Practice #39)
                Password::min(8)
                    ->letters() // Must contain at least one letter
                    ->mixedCase() // Must contain at least one uppercase and one lowercase letter
                    ->numbers() // Must contain at least one number
                    ->symbols() // Must contain at least one special character
                    ->uncompromised(), // Check against known compromised passwords
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'password.uncompromised' => 'This password has been compromised and cannot be used.',
        ];
    }
}
