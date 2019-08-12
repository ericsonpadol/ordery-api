<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foodcategories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id', 191);
            $table->string('food_category_id', 191)
                ->unique(); //uuid version 4
            $table->string('food_category_name', 191);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('foodcategories');
    }
}
