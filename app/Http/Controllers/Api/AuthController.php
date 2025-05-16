<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthLoginRequest;
use App\Services\Auth\AuthService;
use App\Services\User\UserService;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(
        UserService $userService,
        AuthService $authService
    )
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function login(AuthLoginRequest $request)
    {
        $field = $request->input('field');
        $fieldVal = $request->input($field);
        $password = $request->input('password');

        $data = $this->authService->login($field, $fieldVal, $password);
        if (!$data['status']) return self::failResponse(
            code: $data['data']['code'] ?? Response::HTTP_UNAUTHORIZED,
            message: $data['message']
        );

        return self::successResponse(data: $data, message: $data['message']);
    }
}
