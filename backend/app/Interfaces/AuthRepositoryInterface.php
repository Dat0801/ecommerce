<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email);
    public function create(array $data);
}
