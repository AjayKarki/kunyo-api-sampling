<?php

namespace Modules\Application\Http\Services\Auth;

use Carbon\Carbon;
use Foundation\Models\User;
use Illuminate\Http\Request;
use Modules\Application\Http\Controllers\Profile\UpdateProfile;

/**
 * Class AuthService
 * @package Modules\Application\Http\Services\Auth
 */
final class AuthService
{

    /**
     * @var Request
     */
    private Request $request;

    const ACCESS_TOKEN = 'authToken';

    /**
     * AuthService constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return token
     *
     * @param User $user
     * @param bool $remember
     * @return array
     */
    public function getToken(User $user, bool $remember = false): array
    {
        $tokenResult = $user->createToken(AuthService::ACCESS_TOKEN);
        $token = $tokenResult->token;
        if ($remember) {
            $token->expires_at = now()->addWeeks(1);
        }
        $token->save();

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ];
    }

    /**
     * Logout the user
     *
     * @return mixed
     */
    public function logout()
    {
        return $this->request->user()->token()->revoke();
    }

    /**
     * Return the user
     *
     * @return User
     */
    public function user() : User
    {
        return $this->request->user();
    }
}
