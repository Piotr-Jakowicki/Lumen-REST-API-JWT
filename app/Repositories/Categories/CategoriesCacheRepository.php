<?php

namespace App\Repositories\Categories;

use Illuminate\Support\Facades\Cache;

class CategoriesCacheRepository extends CacheBase implements CategoriesRepositoryInterface
{
    protected $repository;

    const TTL = 1800;

    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($params)
    {
        $key = "categories_" . $this->prepareCacheKey($params);

        return Cache::remember($key, 1800, function () use ($params) {
            return $this->repository->get($params);
        });
    }

    public function find(int $id)
    {
        return Cache::remember("categories.$id", self::TTL, function () use ($id) {
            return $this->repository->find($id);
        });
    }
}
