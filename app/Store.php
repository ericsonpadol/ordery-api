<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Log;
use DB;
use Session;

use App\Traits\StatusHttp;
use App\Traits\AccountHelper;

class Store extends Model
{
    use StatusHttp,
        AccountHelper;

    protected $key = 'id';
    protected $table = 'stores';
    protected $userTable = 'users';
    protected $usersVendorsTable = 'users_vendors';
    protected $fillable = [
        'user_account',
        'store_id',
        'store_name',
        'address',
        'city',
        'store_lat',
        'store_long',
        'mobile_number',
        'phone_number',
        'is_always_open',
        'store_opens_at',
        'store_closes_at',
        'zipcode',
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
    }

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function createNewStore(array $params = [])
    {
        //check if user exists
        $user = User::where('user_account', $params['user_account']);

        if (!$user) {
            return [
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_success'),
                'http_code' => $this->getStatusCode404(),
            ];
        }

        //if user exists create the store
        $storeParams = [
            'store_id' => $this->uuidStoreKeyGeneration(),
            'user_account' => $params['user_account'],
            'store_name' => $params['store_name'],
            'address' => $params['address'],
            'city' => $params['city'],
            'mobile_number' => $params['mobile_number'],
            'phone_number' => $params['phone_number'],
            'is_always_open' => $params['is_always_open'],
            'store_opens_at' => $params['store_opens_at'],
            'store_closes_at' => $params['store_closes_at'],
            'store_lat' => $params['store_lat'],
            'store_long' => $params['store_long'],
            'zipcode' => $params['zipcode'],
        ];

        try {
            if ($this->create($storeParams)) {
                return [
                    'message' => __('messages.create_store_success'),
                    'status' => __('messages.status_success'),
                    'http_code' => $this->getStatusCode200(),
                ];
            }
            Log::info(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY: ' . serialize(DB::getQueryLog()));
        } catch(Exception $e) {
            $exception = [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => $this->getStatusCode500()
            ];

            //log on error
            Log::error(__('messages.convo_id_label') . Session::getId() . ' SQL QUERY: ' . serialize(DB::getQueryLog()));
            Log::error(__('messages.convo_id_label') . Session::getId() . 'EXCEPTION: ' . serialize($exception));
            return $exception;
        }
    }

    public function getStoreInformation($id)
    {
        //get store information
        $storeInfo = DB::table($this->table)
            ->join($this->userTable, $this->userTable . '.user_account', '=', $this->table . '.user_account')
            ->select(
                $this->table . '.*',
                $this->userTable . '.email'
            )
            ->where('store_id', $id)
            ->get();

        return $storeInfo;
    }
}
