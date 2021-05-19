<?php

namespace Foundation\Lib;

use Foundation\Models\User;
use Neputer\Supports\BaseConstant;

/**
 * Class Role
 * @package App\Foundation\Lib
 */
final class Role extends BaseConstant
{

    /**
     *  Role Super Admin
     */
    const ROLE_SUPER_ADMIN = 0;

    /**
     * Role Admin
     */
    const ROLE_ADMIN = 1;

    /**
     * Role Customer
     */
    const ROLE_CUSTOMER = 2;

    /**
     * Role Manager
     */
    const ROLE_MANAGER = 3;

    /**
     * Role Distributor
     */
    const ROLE_DISTRIBUTOR = 4;

    /**
     * Role Reseller
     */
    const ROLE_RESELLER = 5;

    /**
     * Role Reseller
     */
    const ROLE_SALES = 6;

    /**
     * @var $current
     */
    public static $current = [
        self::ROLE_SUPER_ADMIN  => 'super-admin',
        self::ROLE_ADMIN        => 'admin',
        self::ROLE_CUSTOMER     => 'customer',
        self::ROLE_MANAGER      => 'manager',
        self::ROLE_DISTRIBUTOR  => 'distributor',
        self::ROLE_RESELLER     => 'reseller',
        self::ROLE_SALES        => 'sales',
    ];

    public static function getSuperAdmin()
    {
        return static::$current[self::ROLE_SUPER_ADMIN];
    }

    public static function getAdmin()
    {
        return static::$current[self::ROLE_ADMIN];
    }

    public static function getCustomer($returnIndex = false)
    {
        return static::get(static::ROLE_CUSTOMER, $returnIndex);
    }

    public static function getManager($returnIndex = false)
    {
        return static::get(static::ROLE_MANAGER, $returnIndex);
    }

    public static function getSalesPerson($returnIndex = false)
    {
        return static::get(static::ROLE_SALES, $returnIndex);
    }

    public static function getHighLevelRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN  => 'super-admin',
            self::ROLE_ADMIN        => 'admin',
            self::ROLE_MANAGER      => 'manager',
            self::ROLE_SALES        => 'sales',
        ];
    }

    /**
     * Get Middleware String
     *
     * @return string
     */
    public static function getMiddlewareString()
    {
        $middleware= 'access:';
        foreach (self::getHighLevelRoles() as $role)
            $middleware .=  $role . ',';
        return substr($middleware, 0, -1);
    }

}
