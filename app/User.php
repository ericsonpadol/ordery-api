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

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const VERIFIED_USER = 'true';
    const UNVERIFIED_USER = 'false';
    const ADMIN_ENABLED = 'true';
    const ADMIN_DISABLED = 'false';

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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        DB::connection()->enableQueryLog();
        $this->_logger = new Logger('USER');
        $this->_logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
    }
    /**
     * return if user is verified
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
        } catch(Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => Copywrite::HTTP_CODE_500
            ];
        }
    }

    public function userRegistration(array $params)
    {
        try {

        } catch(Exception $e) {
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
