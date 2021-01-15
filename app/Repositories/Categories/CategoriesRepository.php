<?php

namespace App\Repositories\Categories;

use App\Models\Category;

class CategoriesRepository implements CategoriesRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function get($params)
    {
        return $this->model->filterBy($params)->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}
