<?php

namespace App\Filters\CategoryFilters;

use App\Abstracts\QueryFilter;
use App\Filters\OrderContract;

class Desc extends QueryFilter implements OrderContract
{
    public function handle(): void
    {
        $this->query->orderBy('name', 'desc');
    }
}
