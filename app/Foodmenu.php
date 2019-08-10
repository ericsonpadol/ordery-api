<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Foodcategory;
use App\Store;
use Log;
use DB;
use App\Traits\AccountHelper;
use App\Traits\StatusHttp;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foodmenu extends Model
{
    use AccountHelper,
        StatusHttp,
        SoftDeletes;

    protected $key = 'id';
    protected $table = 'foodmenus';
    protected $menuTagTable = 'foodmenus_foodcategories_tags';
    protected $fillable = [
        'food_menu_id',
        'food_menu_name',
        'food_menu_description',
        'food_menu_price',
        'store_id',
        'image_uri',
    ];

    protected $hidden = [
        'created_at'
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

    public function foodcategories()
    {
        return $this->belongsToMany('App\Foodcategory');
    }

    public function stores()
    {
        return $this->belongsToMany('App\Store');
    }

    public function createNewMenuItem(array $params = [])
    {
        $menuParams = [
            'food_menu_id' => $this->uuidStoreKeyGeneration(),
            'food_menu_name' => $params['food_menu_name'],
            'food_menu_description' => $params['food_menu_description'],
            'food_menu_price' => $params['food_menu_price'],
            'store_id' => $params['store_id'],
            'image_uri' => $params['image_uri'],
        ];

        try {
            $id = $this->create($menuParams)->id;

            if (!$id) {
                return [
                    'message' => __('messages.error_default'),
                    'http_code' => $this->getStatusCode500(),
                    'status' => __('messages.status_error'),
                ];
            }

            return [
                'menu_id' => $id,
                'message' => __('messages.create_menu_success'),
                'http_code' => $this->getStatusCode200(),
                'status' => __('messages.status_success'),
            ];
        } catch(Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }

    public function storeMenuTags(array $params = [])
    {
        $tags = [
            'store_id' => $params['store_id'],
            'food_menu_id' => $params['food_menu_id'],
            'food_category_id' => $params['food_category_id'],
        ];

        try {
            DB::table($this->menuTagTable)->insert($tags);
        } catch(Exception $e) {
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
