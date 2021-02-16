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
        // make tags only if ctegories are set
        // @todo add makeTags class
        if (isset($attributes['categories'])) {
            $tags = $this->getAllTags($attributes['categories']);
        } else {
            $tags = [];
        }

        Cache::tags(['images', ...$tags])->flush();

        return $this->repository->store($attributes);
    }

    public function update($id, $attributes)
    {
        // todo fix
        if (isset($attributes['categories'])) {
            $tags = $this->getAllTags($attributes['categories']);
        }

        $updatedImage = $this->repository->update($id, $attributes);

        Cache::tags(["images_$id", 'images', ...$tags ?? []])->flush();

        return $updatedImage;
    }

    public function delete($id)
    {
        $image = $this->repository->find($id);

        $ids = $image->categories()->pluck('id');

        // todo fix
        if (count($ids) > 0) {
            $tags = $this->getAllTags($ids);
        }

        $deletedImage = $this->repository->delete($id);

        // todo fix
        Cache::tags(["images_$id", 'images', ...$tags ?? []])->flush();

        return $deletedImage;
    }

    private function getAllTags($ids)
    {
        foreach ($ids as $id) {
            $tags[] = "category_image_$id";
        }

        return $tags;
    }
}
