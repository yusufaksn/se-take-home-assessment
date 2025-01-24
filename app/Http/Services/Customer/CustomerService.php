<?php

namespace App\Http\Services\Customer;

use Illuminate\Support\Facades\DB;

class CustomerService{

    public function index(): \Illuminate\Support\Collection
    {
        return DB::table('customers')
            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'customers.id',
                'customers.name_surname as name',
                'customers.created_at as since',
                DB::raw('SUM(orders.final_price) as revenue')
            )
            ->groupBy('customers.id', 'customers.name_surname', 'customers.created_at')
            ->orderBy('revenue', 'asc')
            ->get();
    }
}
