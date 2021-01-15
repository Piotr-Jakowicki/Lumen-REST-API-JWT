<?php

namespace App\Repositories\Categories;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CategoriesCacheRepository extends CacheBase implements CategoriesRepositoryInterface
{
    protected $repository;
    protected $cache;

    const TTL = 1444; #

    public function __construct(CacheManager $cache, CategoriesRepository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    public function get($params)
    {
        if (!Redis::get($key = "categories" . $this->prepareCacheKey($params))) {
            Redis::set($key, json_encode($result = $this->repository->get($params)));

            return $result;
        } else {
            return json_decode(Redis::get($key));
        }
    }

    public function find(int $id)
    {
        if (!Redis::get($key = "categories.$id")) {
            Redis::set($key, json_encode($result = $this->repository->find($id)));

            return $result;
        } else {
            return json_decode(Redis::get($key));
        }

        // return $this->cache->remember("categories.$id", self::TTL, function ($id) {
        //     return $this->repository->find($id);
        // });
    }
}
