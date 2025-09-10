<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoothBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'booth_type' => 'required|string|max:100',
            'booth_size' => 'required|string|max:50',
            'booth_quantity' => 'required|integer|min:1|max:10',
            'additional_services' => 'nullable|array',
            'additional_services.*' => 'string|max:100',
            'special_requirements' => 'nullable|string|max:1000',
        ];
    }
}


