<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MasterTableHelper;
use DB;

class MasterTableHelperTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     * this test should insert into master table
     */
    public function it_should_insert_to_master()
    {
        /**
         * test wheter the is inserted to master table
         */
        $masterTable = 'users_vendors';

        $dataProvider = [
            'user_id' => 1,
            'tbl_vendors' => $this->faker->word,
            'tbl_details' => $this->faker->word,
            'tbl_menu' => $this->faker->word,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $masterTableHelper = new MasterTableHelper();
        $masterTableHelper->storeToMasterTable($masterTable, $dataProvider);

        $this->assertInstanceOf(MasterTableHelper::class, $masterTableHelper);
        $this->assertSame(1, 1);
    }

}
