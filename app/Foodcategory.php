<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Foodmenu;
use DB;
use Log;

class Foodcategory extends Model
{
    protected $key = 'id';
    protected $table = 'foodcategories';
    protected $fillable = [
        'food_category_id',
        'food_category_name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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
}