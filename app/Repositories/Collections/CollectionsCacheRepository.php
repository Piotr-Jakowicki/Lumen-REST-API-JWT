<?php

namespace App\Repositories\Collections;

use App\Abstracts\AbstractCache;
use App\Interfaces\CollectionsRepositoryInterface;
use App\Repositories\Collections\CollectionsRepository;
use Illuminate\Support\Facades\Cache;

class CollectionsCacheRepository extends AbstractCache implements CollectionsRepositoryInterface
{
    protected $repository;

    const TTL = 1800;

    public function __construct(CollectionsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($params)
    {
        $key = "collections_" . $this->prepareCacheKey($params);

        return Cache::tags('collections')->remember($key, self::TTL, function () use ($params) {
            return $this->repository->get($params);
        });
    }

    public function find(int $id)
    {
        return Cache::tags("collections_$id")->remember("collections_$id", self::TTL, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function store($attributes)
    {
        Cache::tags('collections')->flush();

        return $this->repository->store($attributes);
    }

    public function update($id, $attributes)
    {
        $updatedCollection = $this->repository->update($id, $attributes);

        Cache::tags("collections_$id")->flush();

        return $updatedCollection;
    }

    public function delete($id)
    {
        $deletedCollections = $this->repository->delete($id);

        Cache::tags(["collections_$id", 'collections'])->flush();

        return $deletedCollections;
    }
}
