<?php

namespace App\Utilities\CategoryFilters;

use App\Utilities\OrderContract;
use App\Utilities\QueryFilter;

class Desc extends QueryFilter implements OrderContract
{
    public function handle(): void
    {
        $this->query->orderBy('name', 'desc');
    }
}
