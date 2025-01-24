<?php

namespace App\Http\Services\Order;

trait FindQuantity {
    private function findQuantity($data, $productId){
        return  array_values(array_filter($data, fn($item) => $item->productId == $productId))[0]->quantity;
    }

}
