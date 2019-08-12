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
        'food_category_id',
        'store_id',
        'image_uri',
    ];

    protected $date = [
        'deleted_at'
    ];
    protected $hidden = [
        'created_at',
        'deleted_at'
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
            'food_category_id' => $params['food_category_id'],
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
            'menu_tags' => strtoupper($params['menu_tag']),
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

    public function getAllStoreFoodMenu($foodCategoryId, $storeId)
    {
        $result = Foodmenu::where([
            ['food_category_id', '=', $foodCategoryId],
            ['store_id', '=', $storeId]
        ])->get();

        return $result;
    }

    public function getFoodTags($foodMenuId, $storeId)
    {
        $foodTags = array();
        $tags = DB::table($this->menuTagTable)
            ->where('food_menu_id', $foodMenuId)
            ->where('store_id', $storeId)
            ->select($this->menuTagTable . '.*')
            ->get();

        Log::debug(DB::getQueryLog());
        foreach($tags as $tag) {
            array_push($foodTags, $tag->menu_tags);
        }

        return implode("," , $foodTags);
    }

    public function getAllMenuOnStore($id)
    {
        $Store = new Store();
        $FoodCategory = new Foodcategory();
        try {
            $data = [];
            //Get Store Information
            $storeInfo = $Store->getStoreInformation($id);
            $data = [
                'store_id' =>  $storeInfo->store_id,
                'store_name' => $storeInfo->store_name,
                'email' => $storeInfo->email,
                'image_uri' => $storeInfo->image_uri
            ];

            //build store menu get all food category under this story
            $foodCategoryMenu = $FoodCategory->getAllFoodCategoryWithStoreInfo($storeInfo->store_id);

            if (!isset($foodCategoryMenu['data'])) {
                return [
                    'message' => __('messages.food_category_not_found'),
                    'status' => __('messages.status_error'),
                    'http_code' => $this->getStatusCode404()
                ];
            }
            $counter = 0;

            foreach($foodCategoryMenu['data'] as $foodCategory) {
                $data['menu'][$counter] = [
                    'food_category_id' => $foodCategory->food_category_id,
                    'food_category_name' => $foodCategory->food_category_name,
                    'food_category_desc' => $foodCategory->food_category_desc,
                ];
                //get all store food menu
                $foods = $this->getAllStoreFoodMenu($foodCategory->food_category_id, $storeInfo->store_id);
                foreach($foods as $food) {
                    $data['menu'][$counter]['foods'] = [
                        'food_menu_id' => $food->food_menu_id,
                        'food_menu_name' => $food->food_menu_name,
                        'food_menu_description' => $food->food_menu_description,
                        'food_menu_price' => $food->food_menu_price,
                    ];
                    //get food tags
                    $foodTags = $this->getFoodTags($food->id, $storeInfo->store_id);
                    $data['menu'][$counter]['foods']['tags'] = $foodTags;
                }
            }

            return $data ? $data : [];

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
