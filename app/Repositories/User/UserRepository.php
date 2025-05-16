<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository
{

    public function checkUserAndPassword(string $field, string $fieldValue, string $password): array;
}
