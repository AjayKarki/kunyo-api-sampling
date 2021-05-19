<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Models\User;
use Foundation\Services\UserService;

/**
 * Class RestoreUserAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class RestoreUserAction
{

    private $userService;

    /**
     * RestoreUserAction constructor.
     * @param UserService $userService
     */
    public function __construct( UserService $userService )
    {
        $this->userService = $userService;
    }

    public function __invoke($user)
    {
        $this->userService->query()
            ->withTrashed()
            ->where('id', $user)
            ->restore();

        flash('success', 'Record is restored successfully !');
        return back();
    }

}
