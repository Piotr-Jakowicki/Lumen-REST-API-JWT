<?php

namespace App\Repositories\CategoryImage;

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
        $key = "category_image_$id" . "_" . $this->prepareCacheKey($params);

        return Cache::tags("category_image_$id")->remember($key, self::TTL, function () use ($params, $id) {
            return $this->repository->get($params, $id);
        });
    }
}
