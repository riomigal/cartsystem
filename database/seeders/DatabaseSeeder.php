<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('migrate:fresh');
        User::firstOrCreate(
            [
                'email' => 'john@doe.test'
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@doe.test',
                'password' => Hash::make('12345678')
            ]
        );
        // \App\Models\User::factory(10)->create();
        (new ProductsTableSeeder)->run();
        (new SerialNumbersTableSeeder)->run();
    }
}
