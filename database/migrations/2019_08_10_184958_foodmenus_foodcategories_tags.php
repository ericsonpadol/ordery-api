<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FoodmenusFoodcategoriesTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foodmenus_foodcategories_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('store_id', 191);
            $table->string('food_menu_id', 191);
            $table->string('menu_tags', 191);
            $table->index('store_id');
            $table->index('food_menu_id');
            $table->index('menu_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('foodmenus_foodcategories_tags');
    }
}
