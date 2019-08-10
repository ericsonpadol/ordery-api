<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodmenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foodmenus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('food_menu_id', 191); //uuid version 4
            $table->string('food_menu_name', 191);
            $table->text('food_menu_description');
            $table->double('food_menu_price');
            $table->string('store_id');
            $table->text('food_category_tag'); //combination of food category id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foodmenus');
    }
}
