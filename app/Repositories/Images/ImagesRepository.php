<?php

namespace App\Repositories\Images;

use App\Interfaces\ImagesRepositoryInterface;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImagesRepository implements ImagesRepositoryInterface
{
    protected $model;

    public function __construct(Image $model)
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
        $image = $this->find($id);

        $image->update($attributes);

        return $image;
    }

    public function delete($id)
    {
        $image = $this->find($id);

        $image->delete();

        return $image;
    }
}
