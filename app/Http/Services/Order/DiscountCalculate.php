<?php

namespace App\Http\Services\Order;

use Illuminate\Support\Facades\DB;

class DiscountCalculate
{
    use FindQuantity;

    public function calculate(array $data, array $queryData): array
    {
        $discounts = $this->getDiscountData();
        $result = [];
        $total = 0;
        foreach ($queryData as $value) {
            $quantity = $this->findQuantity(json_decode(json_encode($data)), $value->productId);
            $price = $value->price;
            $totalPrice = $quantity * $price;
            $result[] = [
                'productName' => $value->name,
                'categoryId' => $value->categoryId,
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_price' => $totalPrice
            ];
            $total += $totalPrice;
        }
        $discountResult['totalPrice'] = $total;
        $totalDiscount = 0;

        foreach ($discounts as $value) {
            $minTotal = $value->min_total;
            $categoryId = $value->category_id;
            $minQuantity = $value->min_quantity;
            $countCategoryProduct = $this->arrayCounter($result, $categoryId);

            if ($value->type == "percentage" and $minTotal and $minTotal <= $total and empty($categoryId)) {
                $discountAmount = $this->calculateDiscountedPrice($total, $value->value);
                $total = $total - $discountAmount;
                $discountResult[$value->name]['discountAmount'] = $discountAmount;
                $discountResult[$value->name]["subtotal"] = $total;
                $totalDiscount = $totalDiscount + $discountAmount;
                $discountResult['discountIds'][] = $value->id;
            }

            if ($value->type == "free_item" and $categoryId and $minQuantity) {
                if ($countCategoryProduct >= $minQuantity) {
                    $findPrice = $this->findUnitPrice($result, $categoryId);
                    $freeItem = floor($countCategoryProduct / $minQuantity);
                    $discountAmount = ($findPrice * $freeItem);
                    $total = $total - $discountAmount;
                    $discountResult[$value->name]['discountAmount'] = $discountAmount;
                    $discountResult[$value->name]["subtotal"] = $total;
                    $totalDiscount = $totalDiscount + $discountAmount;
                    $discountResult['discountIds'][] = $value->id;
                }
            }

            if ($value->type == "percentage" and $minQuantity <= $countCategoryProduct and !empty($categoryId)) {
                $discountAmount = $this->calculateDiscountedPrice($this->cheapestProduct($result), $value->value);
                $total = $total - $discountAmount;
                $discountResult[$value->name]['discountAmount'] = $discountAmount;
                $discountResult[$value->name]["subtotal"] = $total;
                $totalDiscount = $totalDiscount + $discountAmount;
                $discountResult['discountIds'][] += $value->id;
            }

        }
        $discountResult['totalDiscounted'] = $totalDiscount;
        $discountResult['discountedTotal'] = $discountResult['totalPrice'] - $totalDiscount;
        return $discountResult;
    }

    private function cheapestProduct($data): float
    {
        return (float)array_reduce($data, function ($carry, $product) {
            if ($carry === null || $product['unit_price'] < $carry['unit_price']) {
                return $product;
            }
            return $carry;
        })['unit_price'];
    }

    private function calculateDiscountedPrice(float $total, float $discountPercentage): float
    {
        if ($total > 0 && $discountPercentage > 0) {
            return ($total * $discountPercentage) / 100;
        } else {
            return $total;
        }
    }

    private function arrayCounter($data, $categoryId): int
    {
        $filtered = array_filter($data, function ($item) use ($categoryId) {
            return isset($item['categoryId']) && $item['categoryId'] === $categoryId;
        });
        return array_reduce($filtered, function ($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
    }

    private function findUnitPrice($data, $categoryIdToFind)
    {
        $filtered = array_filter($data, function ($item) use ($categoryIdToFind) {
            return $item['categoryId'] === $categoryIdToFind;
        });
        $firstItem = reset($filtered);
        return $firstItem['unit_price'] ?? null;
    }

    private function getDiscountData(): array
    {
        return DB::table('discounts')
            ->whereDate('discount_start_date', '<=', now())
            ->whereDate('discount_end_date', '>=', now())
            ->get([
                'discounts.id',
                'discounts.name',
                'type',
                'value',
                'discounts.category_id',
                'min_quantity',
                'min_total'
            ])->toArray();
    }
}
