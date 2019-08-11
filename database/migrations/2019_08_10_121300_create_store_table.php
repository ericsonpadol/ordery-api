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
            $table->increments('id');
            $table->string('user_account', 191);
            $table->string('store_id', 191)
                ->unique();
            $table->string('store_name', 191);
            $table->text('street');
            $table->string('brgy', 191)
                ->nullable();
            $table->string('province', 191)
                ->nullable();
            $table->string('region', 191)
                ->nullable();
            $table->string('city', 191);
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
            $table->double('store_lat')
                ->nullable();
            $table->double('store_long')
                ->nullable();
            $table->integer('zipcode')
                ->unsigned()
                ->default(0);
            $table->text('image_uri')
                ->nullable();
            $table->timestamps();

            //foreign key
            $table->foreign('user_account')
                ->references('user_account')
                ->on('users');
            $table->index('city', 'addr_city');
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
