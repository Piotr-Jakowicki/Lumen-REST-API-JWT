<?php

namespace App\Interfaces;

interface FilterInterface
{
    public function handle($value): void;
}
