<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Models\User;
use Foundation\Services\UserService;

/**
 * Class ForceDeleteUserAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class ForceDeleteUserAction
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
            ->forceDelete();

        flash('success', 'Record is deleted successfully !');
        return;
    }

}
