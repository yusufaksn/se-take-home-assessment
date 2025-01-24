<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 100) as $index) {

            $id = DB::table('products')->insertGetId([
                'name' => $faker->word,
                'stock_code' => strtoupper($faker->bothify('??###')),
                'price' => $faker->randomFloat(2, 10, 200),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);

            DB::table('product_categories')->insert([
                'product_id' => $id,
                'category_id' => rand(1, 50),
                'created_at' => now(),
                'deleted_at' => null,
            ]);

            DB::table('product_quantity')->insert([
                'product_id' => $id,
                'stock_quantity' => rand(1, 200),
                'created_at' => now(),
                'deleted_at' => null,
            ]);

        }

    }

}
