<?php

namespace Modules\Application\Http\Controllers\Auth;

use Throwable;
use Exception;
use Foundation\Lib\Role;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Foundation\Services\RoleService;
use Foundation\Services\UserService;
use Illuminate\Contracts\Auth\Guard as AuthService;
use Modules\Application\Http\Controllers\BaseController;
use Illuminate\Database\DatabaseManager as DatabaseService;
use Modules\Application\Http\Requests\Auth\RegisterRequest;

/**
 * Class RegisterAction
 * @package Modules\Application\Http\Controllers\Auth
 */
final class RegisterAction extends BaseController
{

    private DatabaseService $database;

    private AuthService $auth;

    private UserService $user;

    private RoleService $role;

    public function __construct (
        DatabaseService $databaseService,
        AuthService  $authService,
        UserService $userService,
        RoleService $roleService
    )
    {
        $this->database  = $databaseService;
        $this->auth      = $authService;
        $this->user      = $userService;
        $this->role      = $roleService;
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception|Throwable
     */
    public function __invoke (RegisterRequest $request) : JsonResponse
    {
        try {
            $this->database->beginTransaction();

            $user = $this->user->new(array_merge($request->validated(), [
                'password' => bcrypt($request->get('password')),
            ]));

            $role = $this->role->getId(Role::$current[Role::ROLE_CUSTOMER]);

            if ($user && $role) {
                $user->assignRole((array) $role);
                // @TODO OTP Verification
                // event(new UserRegistered($user));
            }

            $this->database->commit();

            return $this->responseOk(
                null,
                'You have registered successfully.'
            );

        } catch (Exception $exception) {
            $this->database->rollBack();
            return $this->responseError(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Internal Server Error !');
        }
    }

}
