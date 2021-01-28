<?php

namespace App\Filters\CollectionFilters;

use App\Abstracts\AbstractQueryFilter;
use App\Interfaces\OrderInterface;

class Asc extends AbstractQueryFilter implements OrderInterface
{
    public function handle(): void
    {
        $this->query->orderBy('name', 'asc');
    }
}
