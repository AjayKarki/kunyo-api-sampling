<?php

namespace Modules\Application\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Modules\Payment\PaymentService;
use Modules\Application\Http\DTOs\User\UserData;
use Illuminate\Contracts\Auth\Guard as AuthManager;
use Modules\Application\Http\Services\Auth\AuthService;
use Modules\Application\Http\Requests\Auth\LoginRequest;
use Modules\Application\Http\Controllers\BaseController;
use Illuminate\Database\DatabaseManager as DatabaseService;

/**
 * Class LoginAction
 * @package Modules\Application\Http\Controllers\Auth
 */
final class LoginAction extends BaseController
{

    /**
     * @var AuthManager
     */
    private AuthManager $auth;

    /**
     * @var AuthService
     */
    private AuthService $service;

    private PaymentService $paymentService;

    /**
     * LoginAction constructor.
     * @param AuthManager $authManager
     * @param AuthService $authService
     * @param PaymentService $paymentService
     */
    public function __construct (
        AuthManager $authManager,
        AuthService $authService,
        PaymentService $paymentService
    )
    {
        $this->auth           = $authManager;
        $this->service        = $authService;
        $this->paymentService = $paymentService;
    }

    /**
     * Return the personal access token
     *
     * @param LoginRequest $request
     * @return mixed
     */
    public function __invoke(LoginRequest $request) : JsonResponse
    {
        $credentials = [
            'password' => $request->get('password'),
        ];

        if (is_numeric($request->get('username'))) {
            $credentials['phone_number'] = $request->get('username');
        } elseif (filter_var($request->get('username'), FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->get('username');
        }

        if (!$this->auth->attempt($credentials, $request->get('remember_me') ?? true)) {
            return $this->responseUnAuthorized(null);
        }

        $request->user()->update([
            'last_login' => now(),
        ]);

        return $this->responseOk([
            'token'         => $this->service->getToken($request->user()),
            'user'          => UserData::fromModel($request->user()),
            'total_order'   => $this->paymentService->totalOfUser(optional($request->user())->id, [])
        ]);
    }

}
