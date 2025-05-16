<?php

namespace App\Repositories\Auth;

use App\Models\User;
use LaravelEasyRepository\Repository;

interface AuthRepository extends Repository
{
    public function setToken(User $user): array;
}
