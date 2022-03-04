<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->seed();
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_match(){
        $this->seed();
        $user = User::where('name','sergio')->value('password');
        $response = $this->postJson('api/login' , [
            'name' => 'sergio',
            'password' => 'Password1',
       ])->assertStatus(200)->assertJsonStructure([
           'access_Token',
           'token_type'
       ]);


        //var_dump($response);

    }

    public function test_user_not_found(){
        $this->seed();
        $response = $this->postJson('api/login', [
            'name' => 'Pablo',
            'password' => 'Sergiete1'
        ])->assertStatus(401)->assertJsonStructure([
            'message',
        ])->getContent();

        //var_dump($response);
    }

    public function test_user_pass_no_match(){
        $this->seed();
        $response = $this->postJson('api/login', [
            'name' => 'Pablo',
            'password' => 'Sergiete5'
        ])->assertStatus(401)->assertJsonStructure([
            'message',
        ])->getContent();

       // var_dump($response);
    }
}
