<?php

namespace App\Interfaces;

interface CategoryImageRepositoryInterface
{
    public function get(array $params, int $id);
}
