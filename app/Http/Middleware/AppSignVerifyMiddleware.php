<?php

namespace App\Http\Middleware;

use Closure;

class AppSignVerifyMiddleware
{
    public function handle($request, Closure $next)
    {
        $version = $request->input('v', null);
        $verify  = $request->input('verify', null);
        $args    = $request->input();
        if ($version == 2 && $verify !== null) {
            unset($args['verify']);
            unset($args['v']);
            if (!sign($args, $verify)) {
                error('ERROR_SIGN_FAIL', '签名错误');
            }
        }

        return $next($request);
    }
}
