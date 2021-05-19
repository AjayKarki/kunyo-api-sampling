<?php

namespace Modules\Application\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Modules\Application\Http\DTOs\User\UserData;
use Modules\Application\Http\Services\Auth\AuthService;
use Modules\Application\Http\Controllers\BaseController;

/**
 * Class AuthProcessAction
 * @package Modules\Application\Http\Controllers\Auth
 */
final class AuthProcessAction extends BaseController
{

    /**
     * @var AuthService
     */
    private AuthService $service;

    public function __construct( AuthService $authService )
    {
        $this->service = $authService;
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh() : JsonResponse
    {
        return $this->responseOk(
            array_merge(
                $this->service->getToken(auth('api')->refresh()),
                [
                    'user' => auth('api')->refresh()
                ]
            )
        );
    }

    /**
     * Logout the given user
     *
     * @return JsonResponse
     */
    public function logout() : JsonResponse
    {
        $this->service->logout();
        return $this->responseOk(null);
    }

    /**
     * Return the authenticated User
     *
     * @return JsonResponse
     */
    public function user() : JsonResponse
    {
        return $this->responseOk(
            UserData::fromModel($this->service->user())
        );
    }

    public function update()
    {

    }

}
