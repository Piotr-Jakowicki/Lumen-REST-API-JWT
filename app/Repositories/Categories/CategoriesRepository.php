<?php

namespace App\Repositories\Categories;

use App\Interfaces\CategoriesRepositoryInterface;
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
        return $this->model->filterBy($params)->paginate($params['limit'] ?? 10);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($attributes)
    {
        return Category::create($attributes);
    }

    public function update($id, $attributes)
    {
        $category = $this->find($id);

        $category->update($attributes);

        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);

        $category->delete();

        return $category;
    }
}
