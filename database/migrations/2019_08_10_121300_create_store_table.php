<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('user_account', 191);
            $table->string('store_id', 191)
                ->unique();
            $table->string('store_name', 191);
            $table->text('address');
            $table->string('city', 191);
            $table->string('mobile_number', 15)
                ->unique();
            $table->string('phone_number', 191)
                ->unique()
                ->nullable();
            $table->enum('is_always_open', ['true', 'false'])
                ->default('false');
            $table->time('store_opens_at')
                ->default(null)
                ->nullable();
            $table->time('store_closes_at')
                ->default(null)
                ->nullable();
            $table->double('store_lat');
            $table->double('store_long');
            $table->integer('zipcode')
                ->unsigned()
                ->default(0);
            $table->timestamps();
            //foreign key
            $table->foreign('user_account')
                ->references('user_account')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
