<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private $counter = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'name' => 'Product' . $this->counter++,
            'price' => (float) (rand(9, 99) . '.' . rand(0, 99)),
            'quantity' => rand(5, 30),
        ];
    }
}
