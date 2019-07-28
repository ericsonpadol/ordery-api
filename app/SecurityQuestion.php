<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Traits\StatusHttp;

class SecurityQuestion extends Model
{
    use StatusHttp;

    private $_logger = '';
    protected $table = 'security_questions';

    protected $fillable = [
        'security_questions'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        DB::connection()->enableQueryLog();
        $this->_logger = new Logger('SecurityQuestion');
        $this->_logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
    }

    public static function getAllSecurityQuestions()
    {
        $result = SecurityQuestion::all();

        return $result;
    }

    public function storeSecurityQuestionAnswer(array $params = [])
    {
        try {

            DB::table(config('app.db_tables_map.tbl_users_security_questions'))->insert(
                [
                    'user_id' => $params['user_id'],
                    'security_question_id' => $params['security_question_id'],
                    'security_questions_answer' => $params['security_question_answer'],
                    'created_at'=> date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );

            return [
                'message' => __('messages.security_questions_success'),
                'http_code' => StatusHttp::getStatusCode200()
            ];
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
}
