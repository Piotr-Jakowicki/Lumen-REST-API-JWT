<?php

namespace App\Filters\CategoryFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\OrderInterface;

class Desc extends AbstractQueryFilter implements OrderInterface
{
    public function handle(): void
    {
        $this->query->orderBy('name', 'desc');
    }
}
