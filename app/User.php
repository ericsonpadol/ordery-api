<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Session;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\HasApiTokens;


//helpers
use App\SlaveTableHelper;
use App\MasterTableHelper;
use App\Traits\StatusHttp;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, StatusHttp, HasApiTokens;

    const VERIFIED_USER = 'true';
    const UNVERIFIED_USER = 'false';
    const ADMIN_ENABLED = 'true';
    const ADMIN_DISABLED = 'false';
    const VENDOR_MASTER_TABLE = 'users_vendors';

    protected $primaryKey = 'id';
    protected $table = 'users';


    private $_logger = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_verified',
        'account_type',
        'verification_token',
        'mobile_number',
        'remember_token',
    ];

    protected $date = [
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * constructor
     * @param array
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        DB::connection()->enableQueryLog();
        $this->_logger = new Logger('USER');
        $this->_logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
    }

    /**
     * return if user is verified
     * @return string
     */
    public function isVerified()
    {
        return $this->is_verified == USER::VERIFIED_USER;
    }

    public function isAdmin()
    {
        return $this->is_admin == USER::ADMIN_ENABLED;
    }

    public static function generateVerificationCode()
    {
        return str_random(60);
    }

    public static function getUserAccountTypeList()
    {
        return [
            'customer',
            'vendor',
            'rider',
            'sa'
        ];
    }

    public static function getAllUsers()
    {
        try {
            $result = User::all();

            Log::info(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY ALL USER ACCOUNTS: ' . serialize(DB::getQueryLog()));
            Log::info(__('messages.convo_id_label') . Session::getId() . ' RESULT ALL USER ACCOUNTS: ' . $result);

            return $result;
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }

    public function createNewSubscriber($slaveTable, array $params)
    {

    }

    public function userRegistration(array $params)
    {
        $slaveTable = new SlaveTableHelper();
        $slaveTableName = SlaveTableHelper::removeMultiWhitespaceDash('fv_' . $params['addr_city'] . '_' . date('Y'));

        try {
            //check if city for table is not yet being created
            if (isset($params['addr_city']) && !Schema::hasTable($slaveTableName)) {
                //create slave table
                $tableParams = array('table_name' => $slaveTableName);

                $result = $slaveTable->generateVendorSlaveTable($tableParams);

                if ($result === 'error') {
                    return [
                        'message' => __('messages.registration_failure'),
                        'http_code' => $this->getStatusCode500(),
                        'status' => __('messages.status_error'),
                    ];
                }
            }

            $paramsUserAccount = [
                'email' => $params['email'],
                'mobile_number' => $params['mobile_number'],
                'password' => bcrypt($params['password']),
                'is_admin' => isset($params['is_admin']) ? User::ADMIN_ENABLED : User::ADMIN_DISABLED,
                'is_verified' => User::UNVERIFIED_USER,
                'account_type' => $params['account_type'],
                'verification_token' => User::generateVerificationCode(),
            ];

            //create new registered user
            $userAccount = $this->create($paramsUserAccount)->id;

            //LOG execution
            Log::info(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY: ' . serialize(DB::getQueryLog()));

            if (!$userAccount) {
                return [
                    'message' => __('messages.registration_failure'),
                    'http_code' => $this->getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }

            //insert on the master table
            //prepare insert parameters
            $masterParams = [
                'user_id' => $userAccount,
                'tbl_vendors' => $slaveTableName,
                'tbl_details' => null,
                'tbl_menu' => null,
            ];

            $masterLastInsertedId = MasterTableHelper::storeToMasterTable(USER::VENDOR_MASTER_TABLE, $masterParams);

            //LOG execution
            Log::info(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY: ' . serialize(DB::getQueryLog()));

            if (!$masterLastInsertedId) {
                return [
                    'message' => __('messages.registration_failure'),
                    'http_code' => $this->getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }

            //insert into slave table
            //preapare slave insert parameters
            $slaveParams = [
                [
                    'vendor_master_id' => $masterLastInsertedId,
                    'full_name' => $params['full_name'],
                    'addr_street' => isset($params['addr_street']) ? $params['addr_street'] : null,
                    'addr_brgy' => isset($params['addr_brgy']) ? $params['addr_brgy'] : null,
                    'addr_city' => $params['addr_city'],
                    'addr_province' => isset($params['addr_province']) ? $params['addr_province'] : null,
                    'addr_region' => isset($params['addr_region']) ? $params['addr_region'] : null,
                    'addr_zip' => isset($params['addr_zip']) ? $params['addr_zip'] : null,
                    'store_name' => $params['store_name'],
                    'fb_page' => isset($params['fb_page']) ? $params['fb_page'] : null,
                    'website' => isset($params['website']) ? $params['website'] : null,
                ]
            ];

            $slaveInsert = SlaveTableHelper::storeToSlaveTable($slaveTableName, $slaveParams);

            //LOG execution
            Log::info(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY: ' . serialize(DB::getQueryLog()));

            if (!$slaveInsert && !Schema::hasTable($slaveTableName)) {
                return [
                    'message' => __('messages.registration_failure'),
                    'http_code' => $this->getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }

            return [
                'message' => __('messages.registration_success'),
                'http_code' => $this->getStatusCode200(),
                'status' => __('messages.status_success'),
            ];
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => $this->getStatusCode500()
            ];
        }
    }
}
