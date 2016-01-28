<?php

namespace App\Http\Middleware;

use Closure;

class AppSignVerifyMiddleware
{
    public function handle($request, Closure $next)
    {
        $method   = $request->method();
        $version  = $request->get('v', null);
        $verify   = $request->get('verify', null);

        if ('GET' == $method) {
            $args = $_GET; //or post
        } else {
            $args = $_POST; //or post
        }

        if ($version == 2 && $verify !== null) {
            unset($args['verify']);
            if (!sign($args, $verify)) {
                error('ERROR_SIGN_FAIL','签名错误');
            }
        }

        return $next($request);
    }
}
