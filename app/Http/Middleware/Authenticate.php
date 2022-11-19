<?php

namespace Waterline\Http\Middleware;

use Waterline\Waterline;

class Authenticate
{
    public function handle($request, $next)
    {
        return Waterline::check($request) ? $next($request) : abort(403);
    }
}
