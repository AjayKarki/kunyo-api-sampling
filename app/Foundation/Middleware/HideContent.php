<?php

namespace Foundation\Middleware;

use Foundation\Lib\Meta;

/**
 * Class HideContent
 * @package Foundation\Middleware
 */
class HideContent
{

    public function handle($request, \Closure $next)
    {
        if (!auth()->check() && Meta::get('hide_content')) {
            return redirect()->route('auth.login');
        }

        return $next($request);
    }

}
