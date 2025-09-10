<?php

namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Support\Facades\DB;

class TransactionVendorDecorator implements VendorFactory
{
    public function __construct(private VendorFactory $inner)
    {
    }

    public function create(array $data): Vendor
    {
        return DB::transaction(function () use ($data) {
            return $this->inner->create($data);
        });
    }

    public function createFromApplication(VendorApplication $application): Vendor
    {
        return DB::transaction(function () use ($application) {
            return $this->inner->createFromApplication($application);
        });
    }
}


