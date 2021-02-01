<?php

namespace App\Interfaces;

interface CollectionsRepositoryInterface
{
    public function get(array $params);

    public function find(int $id);

    public function store(array $attributes);

    public function update(int $id, array $attributes);

    public function delete(int $id);
}
