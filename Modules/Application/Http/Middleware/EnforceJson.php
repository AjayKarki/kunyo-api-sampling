<?php

namespace Modules\Application\Http\Middleware;

class EnforceJson
{

    public function handle($request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }

}
