<?php

namespace App\Repositories\Categories;

use App\Abstracts\AbstractCache;
use App\Interfaces\CategoryImageRepositoryInterface;
use App\Repositories\CategoryImage\CategoryImageRepository;
use Illuminate\Support\Facades\Cache;

class CategoryImageCacheRepository extends AbstractCache implements CategoryImageRepositoryInterface
{
    protected $repository;

    const TTL = 1800;

    public function __construct(CategoryImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($params, $id)
    {
        dd($this->repository);
        $key = "category_image_" . $this->prepareCacheKey($params);

        // add category_image to image/category cache flush

        return Cache::tags('category_image')->remember($key, self::TTL, function () use ($params, $id) {
            return $this->repository->get($params, $id);
        });
    }
}
