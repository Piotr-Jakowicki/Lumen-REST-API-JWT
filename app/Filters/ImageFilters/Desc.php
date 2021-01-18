<?php

namespace App\Filters\ImageFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\OrderInterface;

class Desc extends AbstractQueryFilter implements OrderInterface
{
    public function handle(): void
    {
        $this->query->orderBy('title', 'desc');
    }
}
