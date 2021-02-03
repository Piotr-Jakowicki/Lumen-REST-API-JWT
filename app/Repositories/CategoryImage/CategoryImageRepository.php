<?php

namespace App\Repositories\CategoryImage;

use App\Interfaces\CategoryImageRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Arr;

class CategoryImageRepository implements CategoryImageRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function get($params, $id)
    {
        $catgory = $this->model
            ->where('id', $id)
            ->with('images')
            ->first();

        return $catgory
            ->images()
            ->filterBy($params)
            ->paginate($params['limit'] ?? 10);
    }
}
