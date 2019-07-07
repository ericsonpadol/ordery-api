<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\SlaveTableHelper;
use App\User;
use Illuminate\Support\Facades\Schema;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function it_should_register_user()
    {

        $dataUser = [
            'email' => $this->faker->email,
            'password' => 'Secret@1234',
            'mobile_number' => '09182421651',
            'is_admin' => false,
            'is_verified' => false,
            'account_type' => 'customer',
            'verification_token' => str_random(60),
            'remember_token' => null
        ];

        $oUser = new User();
        $user = User::create($dataUser);

        $this->assertInstanceOf(User::class, $oUser);
        $this->assertContains('09182421651', $user->mobile_number);
        $this->assertContains('@', $user->email);
    }
}
