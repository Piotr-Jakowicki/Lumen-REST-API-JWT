<?php

namespace App\Repositories\Images;

use App\Abstracts\AbstractCache;
use App\Interfaces\ImagesRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ImagesCacheRepository extends AbstractCache implements ImagesRepositoryInterface
{
    protected $repository;

    const TTL = 1800;

    public function __construct(ImagesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($params)
    {
        $key = "images_" . $this->prepareCacheKey($params);

        return Cache::tags('images')->remember($key, self::TTL, function () use ($params) {
            return $this->repository->get($params);
        });
    }

    public function find(int $id)
    {
        return Cache::tags("images_$id")->remember("images_$id", self::TTL, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function store($attributes)
    {
        Cache::tags(['images', 'category_image'])->flush();

        return $this->repository->store($attributes);
    }

    public function update($id, $attributes)
    {
        $updatedImage = $this->repository->update($id, $attributes);

        Cache::tags(["images_$id", 'images', 'category_image'])->flush();

        return $updatedImage;
    }

    public function delete($id)
    {
        $deletedImage = $this->repository->delete($id);

        Cache::tags(["images_$id", 'images', 'category_image'])->flush();

        return $deletedImage;
    }
}
