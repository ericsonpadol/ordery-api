<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Session;
use stdClass;

//helper or traits
use App\Traits\StatusHttp;

class SlaveTableHelper extends Model
{
    use StatusHttp;

    public static function removeMultiWhitespaceDash($string)
    {
        //remove multiple whitespaces and dashes
        return preg_replace("/[\s-]+/", "_", $string);

    }

    public function generateVendorSlaveTable(array $params)
    {
        $sanitizeString = SlaveTableHelper::removeMultiWhitespaceDash($params['table_name']);

        $sql = 'CREATE TABLE ' . $sanitizeString . '('
            . 'id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, '
            . 'vendor_master_id int UNSIGNED NOT NULL, '
            . 'full_name varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, '
            . 'addr_street text COLLATE utf8mb4_unicode_ci, '
            . 'addr_brgy text COLLATE utf8mb4_unicode_ci, '
            . 'addr_city varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL, '
            . 'addr_province text COLLATE utf8mb4_unicode_ci, '
            . 'addr_region text COLLATE utf8mb4_unicode_ci, '
            . 'addr_zip char(10), '
            . 'store_name varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL, '
            . 'fb_page text COLLATE utf8mb4_unicode_ci, '
            . 'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, '
            . 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, '
            . 'website text COLLATE utf8mb4_unicode_ci, '
            . 'INDEX idx_city(addr_city), '
            . 'INDEX idx_zip(addr_zip), '
            . 'INDEX idx_store(store_name),'
            . 'FOREIGN KEY fk_vendor_master(vendor_master_id) REFERENCES users_vendors(id)'
            .  ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        try {
            if (DB::statement($sql)) {
                return [
                    'message' => __('messages.success_slave_table'),
                    'table_name' => $sanitizeString,
                    'http_code' => $this->getStatusCode200(),
                    'status' => __('messages.status_success'),
                ];
            }

            return [
                'message' => __('messages.error_default'),
                'http_code' => $this->getStatusCode500(),
                'status' => __('messages.status_error'),
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
