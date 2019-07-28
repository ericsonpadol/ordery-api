<?php

namespace App\Traits;

use DB;

trait AccountSecurity
{
    public function isAccountActive(array $params = [], $table = 'users')
    {
        //check if account is active
        $user = DB::table($table)
            ->where('email', $params['email'])
            ->where('is_verified', true)
            ->first();

        return $user ? true : false;
    }
}