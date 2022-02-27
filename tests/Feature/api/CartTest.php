<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{

    private User $user;

    public function setUp(): void
    {

        parent::setUp();
        $this->user = User::firstOrCreate(
            [
                'email' => 'john@doe.test'
            ],
            [
                'name' => 'Johne',
                'email' => 'john@doe.test',
                'password' => Hash::make('12345678'),

            ]
        );
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cart_store_id_missing()
    {
        Sanctum::actingAs($this->user, ['cart:access']);
        $response = $this->post('/api/cart/store');


        $response->assertStatus(422);
    }



    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cart_store_product_sold_out()
    {

        $product = Product::find(1);

        $qty = $product->quantity;

        $product->quantity = 0;
        $product->save();

        Sanctum::actingAs($this->user, ['cart:access']);
        $response = $this->post('/api/cart/store', ['product_id' => 1, 'quantity' => 1]);


        $product->quantity = $qty;
        $product->save();

        $response->assertStatus(202);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cart_store_success()
    {
        Sanctum::actingAs($this->user, ['cart:access']);
        $response = $this->post('/api/cart/store', ['product_id' => 1, 'quantity' => 1]);

        $response->assertStatus(200);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cart_store_multiple_success()
    {
        Sanctum::actingAs($this->user, ['cart:access']);
        $response = $this->post('/api/cart/store', ['product_id' => 1, 'quantity' => 4]);
        dd($response->json());
        $response->assertStatus(200);
    }



    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cart_index()
    {
        Sanctum::actingAs($this->user, ['cart:access']);
        $response = $this->get('/api/cart');



        $response->assertStatus(200);
    }
}
