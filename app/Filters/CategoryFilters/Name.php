<?php

namespace App\Filters\CategoryFilters;

use App\Abstracts\QueryFilter;
use App\Filters\FilterContract;

class Name extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->where('name', 'like', "%$value%");
    }
}
