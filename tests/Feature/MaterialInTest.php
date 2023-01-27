<?php

namespace Tests\Feature;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\User;
use Database\Seeders\MaterialsSeeder;
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

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function material_in_can_be_created()
    {
        $this->init();

        $response = $this->post('material-ins', $this->test_data);

        $response->assertStatus(302);

        $this->assertDatabaseCount('material_ins', 1);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][0]);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][1]);
        $this->assertDatabaseHas('material_in_details', $this->test_data['details'][2]);
    }

    public function test_material_in_can_be_updated()
    {
        $this->init();

        MaterialIn::factory()->create();
        MaterialInDetail::factory(3)->create();

        $response = $this->put("material-ins/1", $this->test_data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('material_in_details', ['qty' => 10]);
        // $this->assertDatabaseHas('material_in_details', $this->test_data['details'][1]);
        // $this->assertDatabaseHas('material_in_details', $this->test_data['details'][2]);
    }

    public function material_in_can_be_deleted()
    {
        $this->init();

        $materialIns = MaterialIn::factory(2)->create();
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

        $response = $this->delete('/material-ins/1');

        $response->assertStatus(302);

        $response = $this->delete('/material-ins/2');

        $response->assertStatus(302);

        // TODO: remove soft delete
        $this->assertSoftDeleted($materialIns[0]);
        $this->assertNotSoftDeleted($materialIns[1]);
    }
}
