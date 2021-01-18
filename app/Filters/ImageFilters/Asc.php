<?php

namespace App\Filters\ImageFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\OrderInterface;

class Asc extends AbstractQueryFilter implements OrderInterface
{
    public function handle(): void
    {
        $this->query->orderBy('title', 'asc');
    }
}
