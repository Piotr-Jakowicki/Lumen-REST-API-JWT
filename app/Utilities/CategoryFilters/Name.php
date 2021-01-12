<?php

namespace App\Utilities\CategoryFilters;

use App\Utilities\FilterContract;
use App\Utilities\QueryFilter;

class Name extends QueryFilter implements FilterContract
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function handle($value): void
    {
        $this->query->where('name', 'like', "%$value%");
    }
}
