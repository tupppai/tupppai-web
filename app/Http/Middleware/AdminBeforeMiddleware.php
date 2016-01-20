<?php

namespace App\Http\Middleware;

use Closure;

class AdminBeforeMiddleware
{
    public function handle($request, Closure $next)
    {
        // Perform action
        app()->register('Collective\Html\HtmlServiceProvider');
        class_alias('Collective\Html\HtmlFacade', 'Html');
        class_alias('Collective\Html\FormFacade', 'Form');

        return $next($request);
    }
}
