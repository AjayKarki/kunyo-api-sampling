<?php

namespace Modules\Application\Http\Controllers\Auth;

use Foundation\Services\UserService;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\Requests\Auth\ChangePasswordRequest;

final class ChangePasswordAction extends BaseController
{

    private UserService $user;

    public function __construct(UserService $userService)
    {
        $this->user = $userService;
    }

    public function __invoke(ChangePasswordRequest $request)
    {
        $this->user->changePassword($request->get('new_password'));

        return $this->responseOk(
            null,
            'Your password is successfully changed!'
        );
    }

}
