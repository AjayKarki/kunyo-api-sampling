<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Foundation\Services\UserService;

final class ResetTwoFA extends BaseController
{

    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request)
    {
        return $this->responseOk(
            $this->service->updateTwoFA($request->get('user_id'))
        );
    }

}
