<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use App\Repositories\User\UserRepository;
use App\Traits\ServiceResponser;
use LaravelEasyRepository\Service;

class AuthServiceImplement extends Service implements AuthService
{
    use ServiceResponser;

    /**
     * don't change $this->mainRepository variable name
     * because used in extent service class
     */
    protected AuthRepository $mainRepository;
    protected UserRepository $userRepository;

    public function __construct(
        AuthRepository $mainRepository,
        UserRepository $userRepository
    )
    {
        $this->mainRepository = $mainRepository;
        $this->userRepository = $userRepository;
    }

    public function login(string $field, string $fieldValue, string $password): array
    {
        $userValid = $this->userRepository->checkUserAndPassword($field, $fieldValue, $password);
        if ($userValid['error']) return self::finalResultFail(message: $userValid['message']);

        $user = $userValid['data'];
        $setToken = $this->mainRepository->setToken($user);
        if ($setToken['error']) return self::finalResultFail(message: $setToken['message']);
        return self::finalResultSuccess(data: $setToken['data'], message: 'Login successfully');
    }
}
