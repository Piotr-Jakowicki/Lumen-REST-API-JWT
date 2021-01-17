<?php

namespace App\Filters\CategoryFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\FilterInterface;

class Name extends AbstractQueryFilter implements FilterInterface
{
    public function handle($value): void
    {
        $this->query->where('name', 'like', "%$value%");
    }
}
