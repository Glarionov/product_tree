<?php

namespace App\Http\Services\ServiceTraits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

trait SearchAbleTrait
{
    /**
     * @param $model
     * @return mixed
     */
    public function getBaseQuery($model): mixed
    {
        return $model::query();
    }

    /**
     * @param Model $model
     * @param string $searchString
     * @param array $searchFields
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function search(Model $model, string $searchString, array $searchFields, int $limit = 100): LengthAwarePaginator
    {
        $query = $this->getBaseQuery($model);
        foreach ($searchFields as $searchField) {
            $query->orWhere($searchField, 'like', "%$searchString%");
        }

        return $query->paginate($limit);
    }
}
