<?php

namespace Tests\Feature;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\User;
use Database\Seeders\MaterialsSeeder;
use Helper;
use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialInTest extends TestCase
{
    use DatabaseMigrations;

    private array $test_data = [
        'code' => 'test1',
        'at' => '2021-01-01',
        'note' => 'Test1',
        'type' => 'Purchase',
        'details' => [
            [
                'material_id' => 1,
                'qty' => 10,
                'price' => 10000
            ], [
                'material_id' => 2,
                'qty' => 20,
                'price' => 20000
            ], [
                'material_id' => 3,
                'qty' => 30,
                'price' => 30000
            ]
        ]
    ];

    private function init()
    {
        $this->seed(MaterialsSeeder::class);

        $user = Helper::createSuperman();
        $this->actingAs($user);
    }

    public function test_material_in_can_be_created()
    {
        $this->init();

        $response = $this->post('material-ins', $this->test_data);

        $response->assertStatus(302);

        $this->assertDatabaseCount('material_ins', 1);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][0]);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][1]);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][2]);
    }

    private function material_in_can_be_updated_assert()
    {
        $this->assertDatabaseCount('material_ins', 1);
        $this->assertDatabaseHas('material_ins', ['code' => 'test2']);

        $this->assertDatabaseCount('material_in_details', 3);
        $this->assertDatabaseHas('material_in_details', ['material_id' => 1]);
        $this->assertDatabaseHas('material_in_details', ['material_id' => 4]);
        $this->assertDatabaseHas('material_in_details', ['material_id' => 3]);
    }

    public function test_material_in_can_be_updated()
    {
        // 0. initialize data

        $this->init();
        $response = $this->post('material-ins', $this->test_data);

        // case 1 data changed before used
        $data1 = $this->test_data;
        $data1['code'] = 'test2';
        $data1['at'] = '2023-01-01 10:10:10';
        $data1['note'] = 'test2';
        $data1['type'] = 'Free';
        $data1['details'][1]['material_id'] = 4; // change material_id
        $data1['details'][2]['qty'] = 40; // change qty

        // test case 1 begin
        $response = $this->put("material-ins/1", $data1);
        $response->assertStatus(302);

        $this->material_in_can_be_updated_assert();

        // case 2 data changed after used
        MaterialOut::factory()->create();
        MaterialOutDetail::insert([
            [
                'material_out_id' => 1,
                'material_in_detail_id' => 1,
                'qty' => 10
            ], [
                'material_out_id' => 1,
                'material_in_detail_id' => 4,
                'qty' => 10
            ]
        ]);

        $data2 = $data1;
        $data2['code'] = 'test3';
        $data2['at'] = '2023-01-02 10:10:10';
        $data2['note'] = 'test3';
        $data2['type'] = 'Test';
        $data2['details'][0]['material_id'] = 5; // change material_id
        $data2['details'][1]['qty'] = 9; // change qty lest than used qty

        // test case 2 begin, should fail and rollback
        $response = $this->put("material-ins/1", $data2);
        $response->assertStatus(302);

        $this->material_in_can_be_updated_assert();
    }

    public function test_material_in_can_be_deleted()
    {
        $this->init();

        MaterialIn::factory(2)->create();
        MaterialInDetail::insert([
            [
                'material_in_id' => 1,
                'material_id' => 1,
                'qty' => 10,
                'price' => 10000
            ], [
                'material_in_id' => 2,
                'material_id' => 2,
                'qty' => 20,
                'price' => 20000
            ]
        ]);

        MaterialOut::factory()->create();
        MaterialOutDetail::create([
            'material_out_id' => 1,
            'material_in_detail_id' => 1,
            'qty' => 10
        ]);

        $response2 = $this->delete('/material-ins/2');
        $response2->assertStatus(302);

        // fail delete because material_in_id 1 is used
        // note: fail response can't be run first
        $response1 = $this->delete('/material-ins/1');
        $response1->assertStatus(302);

        $this->assertDatabaseCount('material_ins', 1);
        $this->assertDatabaseHas('material_ins', ['id' => 1]);
        $this->assertDatabaseMissing('material_ins', ['id' => 2]);
    }
}
