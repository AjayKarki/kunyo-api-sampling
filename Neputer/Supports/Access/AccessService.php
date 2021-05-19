<?php

namespace Neputer\Supports\Access;

use Illuminate\Container\Container;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class AccessService
 * @package Neputer\Supports\Access
 */
final class AccessService
{

    /**
     * The Config repository instance
     *
     * @var $config
     */
    private $config;

    /**
     * AccessService constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Return role model instance
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    private function role()
    {
        return Container::getInstance()->make($this->config->get('permission.role_table'));
    }

    /**
     * Return role model instance
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    private function permission()
    {
        return Container::getInstance()->make($this->config->get('permission.table'));
    }

    /**
     * Assign the permissions to given role
     *
     * @param $role
     * @param array $permissions
     * @return mixed
     */
    public function assignPermission($role, array $permissions)
    {
        return $role->sync($permissions);
    }

    /**
     * Assign the role to given user
     *
     * @param $user
     * @param array $roles
     * @return mixed
     */
    public function assignRole($user, array $roles)
    {
        return $user->sync($roles);
    }

}
