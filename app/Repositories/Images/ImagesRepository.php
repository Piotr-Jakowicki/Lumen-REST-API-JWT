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
        // refactore

        $url = $this->uploadImage($attributes['image']);

        $image = $this->model->create([
            'path' => url() . $url,
            'title' => $attributes['title'],
            'user_id' => Auth::id(),
        ]);

        // $categoryIds = $attributes['categories'];

        // $image->categories()->attach(explode(',', $categoryIds));

        return $image;
    }

    public function update($id, $attributes)
    {
        // refactor logic to service

        $image = $this->find($id);

        // add it to service

        if (isset($attributes['image'])) {
            $oldPath = last(explode('/', $image->path));
            Storage::delete("public/$oldPath");

            $url = $this->uploadImage($attributes['image']);

            $attributes = array_merge($attributes, ['path' => url() . $url]);
        }

        $image->update($attributes);

        return $image;
    }

    public function delete($id)
    {
        $image = $this->find($id);

        $image->delete();

        return $image;
    }

    protected function uploadImage($image)
    {
        $path = Storage::put('public', $image);
        return Storage::url($path);
    }
}
