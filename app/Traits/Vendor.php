<?php

namespace App\Traits;

use DB;
use App\Traits\AccountHelper;

trait Vendor {
    use AccountHelper;

    public static function generateVendorId()
    {
        return AccountHelper::uuidGeneration();
    }

    public function accountTableDetails($userType)
    {
        switch($userType) {
            case 'vendor':
                return 'stores';
                break;
            case 'rider':
                return 'vehicles';
                break;
            case 'customer':
                return 'customers';
            default:
                return 'sa';
        }
    }
}