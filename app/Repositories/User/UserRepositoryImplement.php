<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Traits\RepositoryResponser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use LaravelEasyRepository\Implementations\Eloquent;

class UserRepositoryImplement extends Eloquent implements UserRepository
{
    use RepositoryResponser;

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function checkUserAndPassword(string $field, string $fieldValue, string $password): array
    {
        $user = $this->model->where($field, $fieldValue)->first();
        if (!$user) return self::result(error: true, message: 'Username/Email tidak terdaftar');
        if (!Hash::check($password, $user->password)) return self::result(error: true, message: 'Password salah');
        return self::result(data: $user, message: 'Berhasil login');
    }
}
