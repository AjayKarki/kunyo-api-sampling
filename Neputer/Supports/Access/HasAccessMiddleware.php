<?php

namespace Neputer\Supports\Access;

use Foundation\Lib\Impersonated;

/**
 * Class HasAccessMiddleware
 * @package Neputer\Supports\Access
 */
class HasAccessMiddleware
{

    public function handle($request, \Closure $next)
    {
        if (Impersonated::isImpersonating()) {
            return $next($request);
        }

        $roles = array_slice(func_get_args(), 2);
        if (empty($roles)) {
            $roles = [ \Foundation\Lib\Role::$current[\Foundation\Lib\Role::ROLE_SUPER_ADMIN], ];
        }

        if (auth()->check() && $request->user() && $request->user()->doesNotHaveRole($roles))
        {
            abort(403);
        }

        return $next($request);

    }

}
