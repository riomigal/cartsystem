<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SerialNumber>
 */
class SerialNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(int $product_id = 1)
    {
        return [
            'number' => strToUpper(md5(Str::uuid())),
            'product_id' => $product_id,
        ];
    }
}
