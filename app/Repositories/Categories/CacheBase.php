<?php

namespace App\Repositories\Categories;

abstract class CacheBase
{
    public function prepareCacheKey($params)
    {
        ksort($params);

        $key = http_build_query($params, '', '_');

        return $key;
    }
}
