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
            $table->string('food_menu_id', 191)
                ->unique(); //uuid version 4
            $table->string('food_menu_name', 191);
            $table->text('food_menu_description');
            $table->double('food_menu_price');
            $table->string('food_category_id', 191);
            $table->string('store_id', 191);
            $table->text('image_uri')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('store_id')
                ->references('store_id')
                ->on('stores');
            $table->index('food_category_id');
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
