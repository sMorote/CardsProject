<?php

namespace Tests\Feature;

use App\Models\collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
   /* public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }*/

    public function test_card_create_without_authorization(){
        $response = $this->postJson('api/cardregist')->assertStatus(401)->assertJsonStructure([
            'message',
        ])->getContent();

       // var_dump($response);
    }

    public function test_card_create_without_data(){
        $user = User::factory()->create(['role' => 'Administrador']);
        Sanctum::actingAs($user);
        $response = $this->postJson('api/cardregist')->assertStatus(400)->assertJsonStructure([
            'status',
            'msg' => [
                'info',
                'error'
            ],
        ])->getContent();

        //var_dump($response);
    }

    public function test_card_create_without_good_collection(){
        $this->seed();
        $user = User::factory()->create(['role' => 'Administrador']);
        Sanctum::actingAs($user);
        $response = $this->postJson('api/cardregist',[
            'name' => 'Orco furioso',
            'decription' => 'Es un orco muy furioso',
            'collection' => ''
        ])->assertStatus(400)->assertJsonStructure([
            'status',
            'msg' => [
                'info',
                'error'
            ],
        ])->getContent();

       // var_dump($response);
    }

    public function test_card_create_right_datas(){
        $this->seed();
        $collection = collection::where('name','Orcos')->value('id');
        $user = User::factory()->create(['role' => 'Administrador']);
        Sanctum::actingAs($user);
        $response = $this->postJson('api/cardregist',[
            'name' => 'Orco furioso',
            'description' => 'Es un orco muy furioso',
            'collection' =>  $collection,
        ])->assertStatus(200)->assertJsonStructure([
            'status',
            'msg' => [
                'info',
            ],
        ])->getContent();
        
        //var_dump($response);
    }
}
