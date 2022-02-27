<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SerialNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SerialNumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();


        foreach ($products as $product) {
            SerialNumber::factory($product->quantity)->create(['product_id' => $product->id]);
        }
    }
}
