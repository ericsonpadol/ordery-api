<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Foodmenu;
use DB;
use Log;
use App\Store;
use App\Traits\StatusHttp;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foodcategory extends Model
{
    use SoftDeletes,
        StatusHttp;

    protected $key = 'id';
    protected $table = 'foodcategories';
    protected $fillable = [
        'food_category_id',
        'food_category_name',
        'store_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $data = [
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

    public function foodmenus()
    {
        return $this->hasMany('App\Foodmenu');
    }

    public function getAllFoodCategoryWithStoreInfo()
    {
        //return all food category with store information
        $Store = new Store();

        $result = DB::table($this->table)
            ->join($Store->getStoreTable(), $this->table . '.store_id', '=', $Store->getStoreTable() . '.store_id')
            ->select(
                $this->table . '.store_id',
                $this->table . '.food_category_id',
                $this->table . '.food_category_name',
                $this->table . '.updated_at',
                $Store->getStoreTable() . '.store_name'
            )
            ->get();

        return [
            'data' => $result ? $result : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success')
        ];

    }

    public function getSpecificStoreFoodCategoryWithStoreInfo($id)
    {
        //return all food category with store information
        $Store = new Store();

        $result = DB::table($this->table)
            ->join($Store->getStoreTable(), $this->table . '.store_id', '=', $Store->getStoreTable() . '.store_id')
            ->select(
                $this->table . '.store_id',
                $this->table . '.food_category_id',
                $this->table . '.food_category_name',
                $this->table . '.updated_at',
                $Store->getStoreTable() . '.store_name'
            )
            ->where($this->table . '.store_id', '=', $id)
            ->get();

        return [
            'data' => $result ? $result : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success')
        ];
    }
}
