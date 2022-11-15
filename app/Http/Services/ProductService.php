<?php

namespace App\Http\Services;

use App\Http\Services\ServiceTraits\SearchAbleTrait;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    use SearchAbleTrait;

    /**
     * @param array $requestData
     * @return Product
     */
    public function store(array $requestData)
    {
        $product = new Product();
        $product->fill($requestData);
        $product->save();
        return $product;
    }

    /**
     * @param Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBaseQuery(Product $model)
    {
        return $model::query()->with('category');
    }

    /**
     * @param $requestData
     * @return LengthAwarePaginator
     */
    public function searchProduct($requestData): LengthAwarePaginator
    {
        return $this->search(new Product(), $requestData['searchString'], ['name', 'description']);
    }
}
