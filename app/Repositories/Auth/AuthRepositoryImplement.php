<?php

namespace App\Repositories\Auth;

use App\Models\Auth;
use App\Models\User;
use App\Traits\RepositoryResponser;
use Carbon\CarbonInterval;
use LaravelEasyRepository\Implementations\Eloquent;

class AuthRepositoryImplement extends Eloquent implements AuthRepository
{
    use RepositoryResponser;

    public function setToken(User $user): array
    {
        $user->tokens()->delete();
        $accessTokenExpiration = CarbonInterval::hours(7)->totalMinutes;
        $refreshTokenExpiration = CarbonInterval::days(7)->totalMinutes;
        $accessToken = $user->createToken('access_token', ['access-api'], $accessTokenExpiration)->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], $refreshTokenExpiration)->plainTextToken;

        return self::result(
            data: [
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]
        );
    }
}
