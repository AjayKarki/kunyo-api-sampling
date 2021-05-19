<?php

namespace Foundation\Middleware;

use Closure;

class Check2FA
{

    public function handle($request, Closure $next)
    {
        if (optional(auth()->user())->two_fa_enabled){
            if (session()->has('2fa_verified') && session()->get('2fa_verified') == true){
                return $next($request);
            } else{
                return redirect()->route('admin.2fa.prompt');
            }
        }
        return $next($request);
    }


}
