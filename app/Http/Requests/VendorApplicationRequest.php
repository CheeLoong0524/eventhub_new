<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:1000',
            'business_address' => 'required|string|max:500',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'business_license' => 'required|string|max:100',
            'years_in_business' => 'required|integer|min:0|max:100',
            'annual_revenue' => 'required|string|max:50',
            'number_of_employees' => 'required|integer|min:1|max:10000',
            'previous_events' => 'nullable|string|max:1000',
            'product_categories' => 'required|array|min:1',
            'product_categories.*' => 'string|max:100',
            'target_audience' => 'required|string|max:500',
            'marketing_strategy' => 'required|string|max:1000',
        ];
    }
}


