<?php

namespace App\Http\Services\Order;


use Illuminate\Support\Facades\DB;

class SaveOrderAndDiscount {

    public \Illuminate\Support\Carbon $now;

    public function __construct()
    {
        $this->now = now();
    }
    public function save($data, $result): void
    {
        $orderId = DB::table('orders')->insertGetId([
            'customer_id' => $data['customerId'],
            'discount_total' => $result['data']['totalDiscounted'],
            'final_price' => $result['data']['discountedTotal'],
            'total_amount' => $result['data']['totalPrice'],
            'created_at' => $this->now
        ]);
        $orderProduct = [];
        $stockMovements = [];
        $productQuantity = [];
        foreach ($data['productItems'] as $value) {
            $orderProduct[] = [
                'product_id' => $value['productId'],
                'order_id' => $orderId,
                'quantity' => $value['quantity'],
                'created_at' => $this->now
            ];
            $stockMovements[] = [
                'product_id' => $value['productId'],
                'quantity' => $value['quantity'],
                'type' => 'exit',
                'order_id' => $orderId,
                'created_at' => $this->now
            ];
            $productQuantity[] = [
                'product_id' => $value['productId'],
                'stock_quantity' => $value['quantity']
            ];
        }
        if(isset($result['data']['discountIds'])){
            $orderDiscounts = [];
            foreach ($result['data']['discountIds'] as $value){
                $orderDiscounts[] = [
                    'discount_id' => $value,
                    'order_id' => $orderId,
                    'created_at' => $this->now
                ];
            }
            DB::table('order_discounts')->insert($orderDiscounts);
        }

        DB::table('order_products')->insert($orderProduct);
        DB::table('stock_movements')->insert($stockMovements);


        $query = "UPDATE product_quantity SET stock_quantity = CASE product_id ";
        foreach ($productQuantity as $item) {
            $query .= "WHEN {$item['product_id']} THEN stock_quantity - {$item['stock_quantity']} ";
        }
        $query .= "ELSE stock_quantity END WHERE product_id IN (" . implode(",", array_column($productQuantity, 'product_id')) . ")";
        DB::statement($query);
    }
}
