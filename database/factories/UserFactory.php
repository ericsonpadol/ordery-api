<?php

use Faker\Generator as Faker;
use App\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'remember_token' => str_random(10),
        'mobile_number' => str_pad($faker->randomNumber(), 11, '09', STR_PAD_LEFT),
        'is_verified' => $verified = $faker->randomElement([USER::VERIFIED_USER, USER::UNVERIFIED_USER]),
        'verification_token' => $verified == USER::VERIFIED_USER ? null : USER::generateVerificationCode(),
        'is_admin' => $faker->randomElement([USER::ADMIN_DISABLED, USER::ADMIN_ENABLED]),
        'account_type' => $faker->randomElement(USER::getUserAccountTypeList()),
    ];
});
