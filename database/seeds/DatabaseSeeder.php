<?php

use Illuminate\Database\Seeder;
use App\User;
use App\SecurityQuestion;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        User::truncate();
        SecurityQuestion::truncate();

        $this->call('UserSeeder');
        $this->call('SecurityQuestionSeeder');
    }
}
