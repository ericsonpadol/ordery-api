<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Session;

class SlaveTableHelper extends Model
{
    public function generateVendorSlaveTable(array $params)
    {
        $sql = 'CREATE TABLE ' . $params['table_name'] . '('
            . 'id int(10) unsigned NOT NULL AUTO_INCREMENT,'
            . 'vendor_master_id int(10) NOT NULL UNSIGNED,'
            . 'name varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,'
            . 'addr_street text COLLATE utf8mb4_unicode_ci,'
            . 'addr_brgy text COLLATE utf8mb4_unicode_ci,'
            . 'addr_city text COLLATE utf8mb4_unicode_ci,'
            . 'addr_province text COLLATE utf8mb4_unicode_ci,'
            . 'addr_region text COLLATE utf8mb4_unicode_ci,'
            . 'addr_zip char(10),'
            . 'store_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,'
            . 'fb_page text COLLATE utf8mb4_unicode_ci,'
            . 'website text COLLATE utf8mb4_unicode_ci ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        try {
            $slaveTable = DB::statement($sql);

            if (!$slaveTable) {
                return [
                    'message' => __('messages.error_default'),
                    'http_code' => __('messages.http_500'),
                    'status' => __('messages.status_error'),
                ];
            }

            return [
                'message' => __('messages.success_slave_table'),
                'http_code' => __('messages.http_200'),
                'status' => __('messages.status_success'),
            ];

        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => Copywrite::HTTP_CODE_500
            ];
        }
    }
}
