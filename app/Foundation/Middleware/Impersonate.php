<?php

namespace Foundation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Foundation\Lib\Impersonated;
use Illuminate\Support\Facades\Auth;

/**
 * Class Impersonate
 * @package Foundation\Middleware
 */
final class Impersonate
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Impersonated::isImpersonating())
        {
            Auth::guard('web')->onceUsingId(Impersonated::getID());
        }
        return $next($request);
    }

}
