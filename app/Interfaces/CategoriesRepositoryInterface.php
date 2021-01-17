<?php

namespace App\Interfaces;

interface CategoriesRepositoryInterface
{
    public function get(array $params);

    public function find(int $id);
}
