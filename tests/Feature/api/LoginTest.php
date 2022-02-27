<?php

namespace Tests\Feature\api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_missing_credentials()
    {
        $response = $this->post('/api/login');

        $response->assertStatus(422);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_invalid_credentials()
    {
        $response = $this->post('/api/login', ['email' => 'john@doe.com', 'password' => 'blablabla']);
        $response->assertStatus(401);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_successful()
    {
        $user = User::firstOrCreate(
            [
                'email' => 'john@doe.test'
            ],
            [
                'name' => 'Johne',
                'email' => 'john@doe.test',
                'password' => Hash::make('12345678'),

            ]
        );
        $res = $this->post('/api/login', ['email' => $user->email, 'password' => '12345678']);

        $this->assertAuthenticatedAs($user, 'sanctum');
        $res->assertJson(fn (AssertableJson $json) =>
        $json->has('token'));
    }
}
