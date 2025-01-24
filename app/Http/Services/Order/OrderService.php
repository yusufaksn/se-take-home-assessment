<?php

namespace App\Http\Services\Order;


use Illuminate\Support\Facades\DB;

class OrderService
{

    protected IsStock $isStock;
    protected DiscountCalculate $discountCalculate;
    protected SaveOrderAndDiscount $saveOrderAndDiscount;

    public function __construct()
    {
        $this->isStock = new IsStock();
        $this->discountCalculate = new DiscountCalculate();
        $this->saveOrderAndDiscount = new SaveOrderAndDiscount();
    }


    public function index()
    {
        return DB::select("
                            SELECT
                                o.id,
                                o.customer_id AS customerId,
                                json_agg(
                                    json_build_object(
                                        'productId', p.id,
                                        'quantity', op.quantity,
                                        'unitPrice', p.price,
                                        'totalPrice', op.quantity * p.price
                                    )
                                ) AS items,
                                o.final_price AS total
                            FROM
                                orders AS o
                            JOIN
                                order_products AS op ON op.order_id = o.id
                            JOIN
                                products AS p ON op.product_id = p.id
                            WHERE
                                o.deleted_at IS NULL
                            GROUP BY
                                o.id;
");



    }

    public function store($data)
    {
        $result['message'] = "success";
        $result['status'] = 200;
        $productIds = [];

        foreach ($data['productItems'] as $value) {
            $productIds[] = $value['productId'];
        }

        $queryData = $this->getProductData($productIds);

        if (!empty($queryData)) {
            $stockContResult = $this->isStock->stockControl($data['productItems'], $queryData);
            $result['message'] = $stockContResult;
            if ($stockContResult === True) {
                $result['data'] = $this->discountCalculate->calculate($data['productItems'], $queryData);
                $this->saveOrderAndDiscount->save($data, $result);
            } else {
                $result['data'] = null;
                $result['status'] = 500;
            }
        }

        unset($result['data']['discountIds']);
        return $result;
    }

    private function getProductData($productIds)
    {
        if (!empty($productIds)) {
            return DB::table('products')
                ->where('products.deleted_at', null)
                ->whereIn('products.id', $productIds)
                ->leftJoin('product_quantity', function ($on) {
                    $on->on('products.id', '=', 'product_quantity.product_id');
                })
                ->leftJoin('product_categories', function ($onCategory) {
                    $onCategory->on('products.id', '=', 'product_categories.product_id');
                })
                ->get([
                    "products.id as productId",
                    "products.name",
                    "product_quantity.stock_quantity as quantity",
                    "product_categories.category_id as categoryId",
                    "products.price"
                ])->toArray();
        }

    }

    public function deleteOrder($orderId): void
    {
        DB::table('orders')->where('id', $orderId)->update(['deleted_at' => now()]);
        DB::table('order_discounts')->where('id', $orderId)->update(['deleted_at' => now()]);
        DB::table('order_products')->where('id', $orderId)->update(['deleted_at' => now()]);
    }


}
