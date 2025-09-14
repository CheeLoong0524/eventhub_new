<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Factories;

use App\Models\Vendor;
use App\Models\VendorApplication;

class VendorFactoryManager
{
    public static function createFactory(): VendorFactory
    {
        $basic = new BasicVendorCreator();
        $validation = new ValidationVendorDecorator($basic);
        $logging = new LoggingVendorDecorator($validation);
        $transaction = new TransactionVendorDecorator($logging);
        return $transaction;
    }

    public static function createVendor(array $data): Vendor
    {
        return self::createFactory()->create($data);
    }

    public static function createVendorFromApplication(VendorApplication $application): Vendor
    {
        return self::createFactory()->createFromApplication($application);
    }
}


