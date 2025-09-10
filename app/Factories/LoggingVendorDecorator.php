<?php

namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Support\Facades\Log;

class LoggingVendorDecorator implements VendorFactory
{
    public function __construct(private VendorFactory $inner)
    {
    }

    public function create(array $data): Vendor
    {
        Log::info('Creating vendor', ['user_id' => $data['user_id'] ?? null]);
        $vendor = $this->inner->create($data);
        Log::info('Vendor created', ['vendor_id' => $vendor->id]);
        return $vendor;
    }

    public function createFromApplication(VendorApplication $application): Vendor
    {
        Log::info('Creating vendor from application', ['application_id' => $application->id]);
        return $this->create($application->toArray());
    }
}


