<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('discounts')->insert([
            'name' => '10_PERCENT_OVER_1000',
            'type' => 'percentage',
            'value' => 10,
            'min_total' => 1000,
            'deleted_at' => null,
            'discount_start_date' => now(),
            'discount_end_date' => now()->addYears(2)
        ]);
        DB::table('discounts')->insert([
            'name' => 'BUY_6_TO_FREE_1',
            'type' => 'free_item',
            'value' => 1,
            'category_id' => 2,
            'min_quantity' => 6,
            'deleted_at' => null,
            'discount_start_date' => now(),
            'discount_end_date' => now()->addYears(2)
        ]);
        DB::table('discounts')->insert([
            'name' => 'En Ucuz Ürüne %20 İndirim',
            'type' => 'percentage',
            'value' => 20,
            'category_id' => 1,
            'min_quantity' => 2,
            'deleted_at' => null,
            'discount_start_date' => now(),
            'discount_end_date' => now()->addYears(2)
        ]);

    }
}
