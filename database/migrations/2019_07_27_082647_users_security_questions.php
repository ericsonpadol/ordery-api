<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersSecurityQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_security_questions', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('security_question_id');
            $table->string('security_questions_answer', 191);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('security_question_id')
                ->references('id')
                ->on('security_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_security_questions');
    }
}
