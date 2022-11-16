<?php

namespace App\Http\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * @param int|null $parentId
     * @return int
     */
    protected function getNextIndex(?int $parentId): int
    {
        $maxIndex = Category::query()->where('parent_id', $parentId)->orderBy('index', 'desc')->first();

        if (empty($maxIndex)) {
            $nextIndex = 0;
        } else {
            $nextIndex = $maxIndex->index + 1;
        }
        return $nextIndex;
    }

    /**
     * Recursively returns children of current category with its products
     *
     * @param Category $parentCategory
     * @return array
     */
    public function getChildCategoriesAndProducts(Category $parentCategory): array
    {
        $childCategories = Category::query()
            ->where('parent_id', $parentCategory->id)->orderBy('index')->get();

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
            ->where('parent_id',  null)->orderBy('index')->get();

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

        $requestData['index'] = $this->getNextIndex($requestData['parent_id'] ?? null);
        $category->fill($requestData);
        $category->save();
        return $category;
    }

    /**
     * @param $requestData
     * @param Category $category
     * @return Category
     */
    public function update($requestData, Category $category)
    {
        if (isset($requestData['parent_id'])) {
            if ($requestData['parent_id'] === $category->parent_id) {
                unset($requestData['parent_id']);
            }
        }

        if (isset($requestData['index'])) {
            if ($requestData['index'] < 0) {
                $requestData['index'] = 0;
            }
        }

        if (array_key_exists('parent_id', $requestData)) {

            // Decreasing value of old parent indexes, so [0, 1, 2, 3] after removing "1" will change values to
            // [0, 1 -> X , 2 -> 1, 3->2]
            Category::query()->where('index', '>', $category->index)
                ->where('parent_id', $category->parent_id)->decrement('index');

            $newParentId = $requestData['parent_id'];
            $nextIndex = $this->getNextIndex($newParentId);

            if (isset($requestData['index'])) {
                $newIndex = $requestData['index'];
                if ($newIndex >= $nextIndex) {
                    $newIndex = $nextIndex;
                } else {
                    Category::query()->where('index', '>=', $newIndex)
                        ->where('parent_id', $newParentId)->increment('index');
                }

            } else {
                $newIndex = $nextIndex;
            }

            $requestData['index'] = $newIndex;
        } elseif (isset($requestData['index']) && $requestData['index'] != $category->index) {
                $categoryIndex = $category->index;
                $newIndex = $requestData['index'];

                $nextIndex = $this->getNextIndex($category->parent_id);

                if ($newIndex > $nextIndex) {
                    if ($nextIndex !== 0) {
                        $nextIndex--;
                    }
                    $newIndex = $nextIndex;
                    $requestData['index'] = $newIndex;
                }

                $minIndex = min($categoryIndex, $newIndex);
                $maxIndex = max($categoryIndex, $newIndex);

                $query = Category::query()->where('index', '>=', $minIndex)->where('index', '<=', $maxIndex)
                ->where('parent_id', $category->parent_id);

                if ($category->index > $requestData['index']) {
                    $query->increment('index');
                } else {
                    $query->decrement('index');
                }
        }

        $category->fill($requestData);
        $category->save();
        return $category;
    }
}
