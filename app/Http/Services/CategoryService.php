<?php

namespace App\Http\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Recursively returns children of current category with its products
     *
     * @param Category $parentCategory
     * @return array
     */
    public function getChildCategoriesAndProducts(Category $parentCategory): array
    {
        $childCategories = Category::query()
            ->where('parent_id', $parentCategory->id)->get();

        $result = ['category' => $parentCategory->withoutRelations(), 'products' => $parentCategory->products, 'branches' => []];
        foreach ($childCategories as $childCategory) {
            $result['branches'][] = $this->getChildCategoriesAndProducts($childCategory);
        }
        return $result;
    }

    /**
     * Recursively returns all categories
     *
     * @return array
     */
    public function getCategoriesTree(): array
    {
        $result = [];

        $childCategories = Category::query()->with('products')
            ->where('parent_id',  null)->get();

        foreach ($childCategories as $childCategory) {
            $result[] = $this->getChildCategoriesAndProducts($childCategory);
        }
        return $result;
    }

    /**
     * @param array $requestData
     * @return Category
     */
    public function store(array $requestData)
    {
        $category = new Category();
        $category->fill($requestData);
        $category->save();
        return $category;
    }
}
