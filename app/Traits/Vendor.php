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
}