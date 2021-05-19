<?php


namespace App\Http\Controllers\Admin\Actions;

use Foundation\Services\RoleService;
use Foundation\Services\UserService;
use Illuminate\Http\Request;

/**
 * Class EmployerAction
 * @package App\Http\Controllers\Admin\Actions
 */
class EmployerAction
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * EmployerAction constructor.
     * @param UserService $userService
     * @param RoleService $roleService
     */
    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Count Employers by their status
     *
     * @param Request $request
     * @return mixed
     */
    public function countByStatus(Request $request)
    {
        $data['status'] = $this->userService->getCountByStatus($request->get('type'), $request->get('role'));
        return $data;
    }
}
