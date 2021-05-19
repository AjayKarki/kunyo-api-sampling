<?php

namespace Modules\Application\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Foundation\Services\UserService;
use Modules\Application\Http\Controllers\BaseController;

class ChangeNotificationChannel extends BaseController
{

    private UserService $user;

    public function __construct(UserService $userService)
    {
        $this->user = $userService;
    }

    public function __invoke(Request $request)
    {
        if (auth()->user()) {
            $this->user->update([
                'is_sms_enabled'   => $request->get('is_sms_enabled') ? true : false ,
                'is_email_enabled' => $request->get('is_sms_enabled') ? false : true ,
            ], auth()->user());
        }

        return $this->responseOk(
            null,
            'You have successfully changed the notification channel.'
        );
    }

}
