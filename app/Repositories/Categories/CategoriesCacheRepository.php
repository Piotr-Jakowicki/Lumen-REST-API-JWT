<?php

namespace App\Repositories\Categories;

use Illuminate\Support\Facades\Cache;

class CategoriesCacheRepository extends CacheAbstract implements CategoriesRepositoryInterface
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

        return Cache::remember($key, self::TTL, function () use ($params) {
            return $this->repository->get($params);
        });
    }

    public function find(int $id)
    {
        return Cache::remember("categories_$id", self::TTL, function () use ($id) {
            return $this->repository->find($id);
        });
    }
}
