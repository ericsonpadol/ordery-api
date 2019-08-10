<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('user_account', 191)
                ->unique();
            $table->string('email', 100);
            $table->string('password');
            $table->string('mobile_number', 11);
            $table->char('is_admin', 10)->default(USER::ADMIN_DISABLED);
            $table->char('is_verified', 10)->default(USER::UNVERIFIED_USER);
            $table->enum('account_type', USER::getUserAccountTypeList());
            $table->string('verification_token', 60)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('email', 'email');
            $table->unique('mobile_number', 'mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
