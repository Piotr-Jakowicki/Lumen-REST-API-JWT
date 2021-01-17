<?php

namespace App\Abstracts;

abstract class AbstractQueryFilter
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }
}
