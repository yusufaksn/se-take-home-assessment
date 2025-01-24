<?php


namespace App\Http\Services\Product;


use Illuminate\Support\Facades\DB;

class ProductService{

    public function index(): \Illuminate\Support\Collection
    {
       return DB::table('products')
            ->leftJoin('product_categories', function($productCategoryOn){
                $productCategoryOn->on('products.id', '=', 'product_categories.product_id');
            })
            ->leftJoin('product_quantity', function($productQuantityOn){
                $productQuantityOn->on('product_quantity.product_id', '=', 'products.id');
            })
            ->get([
               'products.id as id',
               'products.name as name',
               'product_categories.category_id as category',
               'products.price',
               'product_quantity.stock_quantity as stock'
            ]);
    }

}
