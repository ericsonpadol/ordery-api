<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\SlaveTableHelper;
use Illuminate\Support\Facades\Schema;

class SlaveTableHelperTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     * test slave table creation
     * @return void
     */
    public function it_can_create_a_slave_table()
    {
        $data = array('table_name' => 'fv_' . $this->faker->city . '_' . date('Y'));
        $sanitizeString = SlaveTableHelper::removeMultiWhitespaceDash($data['table_name']);

        $slaveTableHelper = new SlaveTableHelper();
        $slaveTable = $slaveTableHelper->generateVendorSlaveTable($data);

        //drop table if exists
        Schema::dropIfExists($sanitizeString);

        $this->assertInstanceOf(SlaveTableHelper::class, $slaveTableHelper);
        $this->assertContains($sanitizeString, $slaveTable);
    }

    /**
     * @test
     * test sanitization
     * @return void
     */
    public function it_can_sanitize()
    {

    }
}
