<?php

namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;

interface VendorFactory
{
    public function create(array $data): Vendor;
    public function createFromApplication(VendorApplication $application): Vendor;
}


