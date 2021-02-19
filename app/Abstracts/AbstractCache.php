<?php

namespace App\Abstracts;

use Illuminate\Support\Str;

abstract class AbstractCache
{
    private $tags = [];

    protected function prepareCacheKey($params)
    {
        ksort($params);

        $key = http_build_query($params, '', '_');

        return $key;
    }

    protected function getAllTags($ids)
    {
        foreach ($ids as $id) {
            $this->tags[] = "category_image_$id";
        }

        return $this->tags;
    }

    private function resourceTags()
    {
    }

    private function relationshipTags()
    {
    }
}
