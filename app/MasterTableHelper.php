<?php

namespace App;

use DB;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Session;

use Illuminate\Database\Eloquent\Model;

class MasterTableHelper extends Model
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        DB::connection()->enableQueryLog();
        $this->_logger = new Logger('MasterTableHelper');
        $this->_logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
    }

    /**
     * Insert data into master table
     * @param String $masterTable
     * @param Array $tblParams #please include user account_id
     * @return Integer
     */
    public static function storeToMasterTable($masterTable, array $tblParams)
    {
        try {
            return DB::table($masterTable)->insertGetId($tblParams);
        } catch (Exception $e) {

            //Log on error
            Log::error(__('messages.convo_id_label') . Session::getId() . ' | ' . __('messages.log_error_label') . $e->getCode() . ' | ' . __('messages.log_error_message') . $e->getMessage());

            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }
}
