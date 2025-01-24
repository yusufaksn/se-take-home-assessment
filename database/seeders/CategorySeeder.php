<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];


        foreach (range(1, 50) as $index) {
            $name = $faker->words(2, true);
            $data[] = [
                'name' => ucfirst($name),
                'slug' => Str::slug($name),
                'description' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }


        DB::table('categories')->insert($data);
    }
}
