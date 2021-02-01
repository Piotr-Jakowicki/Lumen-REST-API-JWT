<?php

namespace App\Repositories\Collections;

use App\Interfaces\CollectionsRepositoryInterface;
use App\Models\Collection;

class CollectionsRepository implements CollectionsRepositoryInterface
{
    protected $model;

    public function __construct(Collection $model)
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
        return $this->model->create($attributes);
    }

    public function update($id, $attributes)
    {
        $collection = $this->find($id);

        $collection->update($attributes);

        return $collection;
    }

    public function delete($id)
    {

        $collection = $this->find($id);

        $collection->delete();

        return $collection;
    }
}
