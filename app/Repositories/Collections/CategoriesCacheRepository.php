<?php

namespace App\Repositories\Categories;

use App\Abstracts\AbstractCache;
use App\Interfaces\CategoriesRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CollectionsCacheRepository extends AbstractCache implements CategoriesRepositoryInterface
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

        return Cache::tags('categories')->remember($key, self::TTL, function () use ($params) {
            return $this->repository->get($params);
        });
    }

    public function find(int $id)
    {
        return Cache::tags("categorie_$id")->remember("categories_$id", self::TTL, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function store($attributes)
    {
        Cache::tags('categories')->flush();

        return $this->repository->store($attributes);
    }

    public function update($id, $attributes)
    {
        $updatedCategory = $this->repository->update($id, $attributes);

        Cache::tags("categorie_$id")->flush();

        return $updatedCategory;
    }

    public function delete($id)
    {
        $deletedCategory = $this->repository->delete($id);

        Cache::tags(["categorie_$id", 'categories'])->flush();

        return $deletedCategory;
    }
}
