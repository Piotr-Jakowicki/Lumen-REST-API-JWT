<?php

namespace App\Abstracts;

abstract class AbstractCache
{
    protected function prepareCacheKey($params)
    {
        ksort($params);

        $key = http_build_query($params, '', '_');

        return $key;
    }
}
