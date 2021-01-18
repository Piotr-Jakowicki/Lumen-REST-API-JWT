<?php

namespace App\Filters\ImageFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\FilterInterface;

class Title extends AbstractQueryFilter implements FilterInterface
{
    public function handle($value): void
    {
        $this->query->where('title', 'like', "%$value%");
    }
}
