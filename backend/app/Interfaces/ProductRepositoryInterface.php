<?php

namespace App\Interfaces;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getFiltered($filters = []);
}
