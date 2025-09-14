<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;

class BasicVendorCreator implements VendorFactory
{
    public function create(array $data): Vendor
    {
        $data['status'] = $data['status'] ?? 'approved';
        return Vendor::create($data);
    }

    public function createFromApplication(VendorApplication $application): Vendor
    {
        return $this->create([
            'user_id' => $application->user_id,
            'company_name' => $application->company_name,
            'company_description' => $application->company_description,
            'business_registration_number' => $application->business_registration_number,
            'contact_person' => $application->contact_person,
            'contact_phone' => $application->contact_phone,
            'contact_email' => $application->contact_email,
            'business_address' => $application->business_address,
            'service_type' => $application->service_type,
            'service_description' => $application->service_description,
            'service_categories' => $application->service_categories,
            'website' => $application->website,
            'social_media' => $application->social_media,
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
}


