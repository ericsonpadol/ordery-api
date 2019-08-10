<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Foodcategory;

class Foodmenu extends Model
{
    //
    public function foodcategories()
    {
        return $this->belongsToMany('App\Foodcategory');
    }
}
