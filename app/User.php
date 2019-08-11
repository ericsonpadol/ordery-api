<?php

namespace App;

//dependency
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

//Models
use App\Store;

//helpers
use App\SlaveTableHelper;
use App\MasterTableHelper;
use App\Traits\StatusHttp;
use App\Traits\AccountSecurity;
use App\Traits\Vendor;
use App\Traits\Rider;
use App\Traits\Customer;

class User extends Authenticatable
{
    use Notifiable,
        SoftDeletes,
        StatusHttp,
        HasApiTokens,
        AccountSecurity,
        Rider,
        Customer,
        Vendor;

    const VERIFIED_USER = 'true';
    const UNVERIFIED_USER = 'false';
    const ADMIN_ENABLED = 'true';
    const ADMIN_DISABLED = 'false';
    const VENDOR_MASTER_TABLE = 'users_vendors';

    public $userVerificationCode;
    protected $primaryKey = 'id';
    protected $table = 'users';
    private $_logger = '';
    private $_paramsProfile = '';

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
        'user_account',
        'image_uri',
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

    public function accounts()
    {
        return $this->hasMany(App\SocialAccount::class);
    }

    public function stores()
    {
        return $this->hasMany('App\Store');
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

    public function getUserDetails($id)
    {
        //check user on the master table
        $userMaster = DB::table(USER::VENDOR_MASTER_TABLE)
            ->where('user_id', '=', $id)
            ->first();

        if (!$userMaster) {
            return [
                'message' => __('messages.error_processing'),
                'http_code' => StatusHttp::getStatusCode500(),
                'status' => __('messages.status_error'),
            ];
        }

        //get user account type
        $accountType = DB::table($this->table)
            ->where($this->table . '.user_account', '=', $id)
            ->select($this->table . '.account_type')
            ->first();
        $lookupTable = $this->accountTableDetails($accountType->account_type);

        //get user account information
        $userAccount = DB::table(USER::VENDOR_MASTER_TABLE)
            ->join($this->table, $this->table . '.user_account', '=', USER::VENDOR_MASTER_TABLE . '.user_id')
            ->where(USER::VENDOR_MASTER_TABLE . '.user_id', '=', $id)
            ->select(
                $this->table . '.email',
                $this->table . '.mobile_number',
                $this->table . '.is_admin',
                $this->table . '.is_verified',
                $this->table . '.account_type',
                $this->table . '.created_at',
                USER::VENDOR_MASTER_TABLE . '.id',
                USER::VENDOR_MASTER_TABLE . '.tbl_vendors'
            )
            ->first();

        //get user details union query
        $userDetails = DB::table($userAccount->tbl_vendors)
            ->where($userAccount->account_type . '_master_id', '=', $userAccount->id)
            ->first();

        //get additional information
        $additionalDetails = DB::table($lookupTable)
                ->where($lookupTable . '.user_account', '=', $id)
                ->first();

        $userDetails = (object) array_merge((array) $userDetails, (array) $additionalDetails);
        $userInfo = (object) array_merge((array) $userAccount, (array) $userDetails);

        Log::debug(__('messages.convo_id_label') .  Session::getId() . serialize(DB::getQueryLog()));

        return [
            'data' => $userInfo ? $userInfo : [],
            'http_code' => StatusHttp::getStatusCode200(),
            'status' => __('messages.status_success'),
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

    public static function deactivateAccount($id)
    {
        try {
            if (User::find($id)->delete()) {
                return [
                    'message' => __('messages.useraccount_deactivated'),
                    'http_code' => StatusHttp::getStatusCode200(),
                    'status' => __('messages.status_success')
                ];
            } else {
                return [
                    'message' => __('messages.error_processing'),
                    'http_code' => StatusHttp::getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }
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

    public static function restoreAccount($id)
    {
        try {
            if (User::withTrashed()
                ->where('id', $id)
                ->restore()
            ) {
                return [
                    'message' => __('messages.useraccount_restored'),
                    'http_code' => StatusHttp::getStatusCode200(),
                    'status' => __('messages.status_success')
                ];
            } else {
                return [
                    'message' => __('messages.error_processing'),
                    'http_code' => StatusHttp::getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }
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

    public function updateUserAccount(array $params)
    {
        $this->_paramsProfile = $params;

        try {
            if (empty($this->_paramsProfile)) {
                return [
                    'message' => __('messages.error_processing'),
                    'http_code' => StatusHttp::getStatusCode400(),
                    'status' => __('messages.status_error'),
                ];
            }

            //find user account
            $user = User::find($this->_paramsProfile['id']);

            //fetch user account on vendors master table
            $userProfile = DB::table('users_vendors')
                ->select('tbl_vendors', 'id')
                ->where('user_id', $this->_paramsProfile['id'])
                ->get();

            //update user profile details
            if ($user->update($this->_paramsProfile) && $userProfile) {
                //temporary blacklist keys
                $tempRemoveKeys = ['email', 'mobile_number', 'account_type'];
                $tempParamsProfile = array_diff_key($this->_paramsProfile, array_flip($tempRemoveKeys));
                $userProfile->map(function ($master) use ($tempParamsProfile) {
                    DB::table($master->tbl_vendors)
                        ->where('vendor_master_id', $master->id)
                        ->update($tempParamsProfile);
                });

                return [
                    'message' => __('messages.useraccount_update_success'),
                    'http_code' => StatusHttp::getStatusCode200(),
                    'status' => __('messages.status_success'),
                ];
            } else {
                return [
                    'message' => __('messages.error_processing'),
                    'http_code' => StatusHttp::getStatusCode400(),
                    'status' => __('messages.status_error'),
                ];
            }
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

    public function userRegistration(array $params)
    {
        $this->userVerificationCode = User::generateVerificationCode();
        $slaveTable = new SlaveTableHelper();
        //check if customer account type
        switch ($params['account_type']) {
            case ('vendor'):
                $tablePrefix = 'vendor_';
                break;
            case ('sa'):
                $tablePrefix = 'sa_';
                break;
            case ('rider'):
                $tablePrefix = 'rider_';
                break;
            default:
                $tablePrefix = 'customer_';
                break;
        }
        $slaveTableName = SlaveTableHelper::removeMultiWhitespaceDash($tablePrefix . $params['addr_city'] . '_' . date('Y'));

        try {
            //check if city for table is not yet being created
            if (isset($params['addr_city']) && !Schema::hasTable($slaveTableName)) {
                //create slave table
                $tableParams = array('table_name' => $slaveTableName , 'account_type' => $params['account_type']);

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
                'is_verified' => User::VERIFIED_USER,
                'account_type' => $params['account_type'],
                'verification_token' => $this->userVerificationCode,
                'user_account' => $this->generateVendorId(),
            ];

            //create new registered user
            $this->create($paramsUserAccount);
            $userAccount = $paramsUserAccount['user_account'];

            /**
             * Happy Path Scenario : Create Store Upon Registration
             */
            //create store
            $storeParams = [
                'user_account' => $userAccount,
                'store_id' => $this->uuidStoreKeyGeneration(),
                'store_name' => $params['store']['store_name'],
                'street' => $params['store']['street'],
                'brgy' => isset($params['store']['brgy']) ? $params['store']['brgy'] : null,
                'province' => isset($params['store']['province']) ? $params['store']['province'] : null,
                'region' => isset($params['store']['region']) ? $params['store']['region'] : null,
                'city' => $params['addr_city'],
                'store_lat' => isset($params['store']['store_lat']) ? $params['store']['store_lat'] : null,
                'store_long' => isset($params['store']['store_long']) ? $params['store']['store_long'] : null,
                'phone_number' => isset($params['store']['phone_number']) ? $params['store']['phone_number'] : null,
                'is_always_open' => $params['store']['is_always_open'],
                'store_opens_at' => isset($params['store']['store_opens_at']) ? $params['store']['store_opens_at'] : null,
                'store_closes_at' => isset($params['store']['store_closes_at']) ? $params['store']['store_closes_at'] : null,
                'zipcode' => isset($params['store']['zipcode']) ? $params['store']['zipcode'] : 0,
                'image_uri' => isset($params['store']['image_uri']) ? $params['store']['image_uri'] : null,
            ];

            Store::create($storeParams);

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
                    $params['account_type'] . '_master_id' => $masterLastInsertedId,
                    'full_name' => $params['full_name'],
                    'addr_street' => isset($params['addr_street']) ? $params['addr_street'] : null,
                    'addr_brgy' => isset($params['addr_brgy']) ? $params['addr_brgy'] : null,
                    'addr_city' => $params['addr_city'],
                    'addr_province' => isset($params['addr_province']) ? $params['addr_province'] : null,
                    'addr_region' => isset($params['addr_region']) ? $params['addr_region'] : null,
                    'addr_zip' => isset($params['addr_zip']) ? $params['addr_zip'] : null,
                    'store_name' => $params['store']['store_name'],
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
