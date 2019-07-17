<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userQuantity = 20;

        factory(User::class, $userQuantity)->create();

        //inject power user
        $powerUser = [
            'email' => 'ericson.padol@gadgetlabsinc.com',
            'password' => bcrypt('secret'),
            'remember_token' => str_random(10),
            'mobile_number' => '09472421651',
            'is_verified' => USER::VERIFIED_USER,
            'verification_token' => null,
            'is_admin' => USER::ADMIN_ENABLED,
            'account_type' => 'sa',
        ];

        User::create($powerUser);
    }
}
