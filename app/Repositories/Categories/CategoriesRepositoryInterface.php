<?php

namespace App\Repositories\Categories;

interface CategoriesRepositoryInterface
{
    public function get(array $params);

    public function find(int $id);
}
