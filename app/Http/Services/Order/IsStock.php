<?php

namespace App\Http\Services\Order;

class IsStock {

    use FindQuantity;

    public function stockControl(Array $data, Array $queryData): bool|string{
        $result = "";
        foreach ($data as $value){
            if($this->findQuantity($queryData, $value['productId']) < $value['quantity']){
                $result .= "Product Id:". $value['productId']. " stock is not enough, ";
            }
        }
        if(empty($result)){
            return true;
        }else{
            return $result;
        }
    }

}
