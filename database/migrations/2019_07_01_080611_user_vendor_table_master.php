<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserVendorTableMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('users_vendors', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            //columns
            $table->increments('id');
            $table->integer('user_id')->unsigned(); //foreign key
            $table->text('tbl_vendors')->nullable();
            $table->text('tbl_details')->nullable();
            $table->text('tbl_menu')->nullable();

            //foreign key
            $table->foreign('user_id')
                ->references('id')
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
        Schema::dropIfExists('users_vendors');
    }
}
