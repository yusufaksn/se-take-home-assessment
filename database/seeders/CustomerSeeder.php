<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];


        foreach (range(1, 100) as $index) {
            $data[] = [
                'name_surname' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'username' => $faker->unique()->userName,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }


        DB::table('customers')->insert($data);
    }
}
