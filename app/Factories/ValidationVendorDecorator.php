<?php

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
            'company_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string',
            'contact_person' => 'required|string',
            'contact_phone' => 'required|string',
            'contact_email' => 'required|email',
            'business_address' => 'required|string',
            'service_type' => 'required|string',
            'service_description' => 'required|string',
        ]);

        $validator->validate();

        return $this->inner->create($data);
    }

    public function createFromApplication(VendorApplication $application): Vendor
    {
        return $this->create($application->toArray());
    }
}


