<?php

// Author  : Choong Yoong Sheng (Vendor module)

namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class ValidationVendorDecorator implements VendorFactory
{
    public function __construct(private VendorFactory $inner)
    {
    }

    public function create(array $data): Vendor
    {
        $validator = ValidatorFacade::make($data, [
            'user_id' => 'required|exists:users,id',
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:500',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'years_in_business' => 'required|string|max:20',
            'business_size' => 'required|string|max:20',
            'annual_revenue' => 'required|string|max:20',
            'event_experience' => 'required|string|max:20',
            'product_category' => 'required|string|max:100',
            'target_audience' => 'required|string|max:100',
            'marketing_strategy' => 'required|string|max:300',
            'status' => 'nullable|string|in:pending,approved,rejected,suspended',
        ]);

        $validator->validate();

        return $this->inner->create($data);
    }

    public function createFromApplication(VendorApplication $application): Vendor
    {
        return $this->create($application->toArray());
    }
}


