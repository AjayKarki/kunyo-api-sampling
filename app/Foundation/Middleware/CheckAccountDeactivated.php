<?php

namespace Foundation\Middleware;

/**
 * Class CheckAccountDeactivated
 * @package Foundation\Middleware
 */
class CheckAccountDeactivated
{

    public function handle($request, \Closure $next)
    {
        if (auth()->check() && auth()->user()->is_deactivated) {
            auth()->logout();

            return redirect()->route('auth.login')
                ->with('deactivated-error', 'Your account is suspended.');
        }

        return $next($request);
    }

}
