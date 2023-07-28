<?php

namespace Nebula\Interfaces\Middleware;

use Nebula\Interfaces\Http\{Response, Request};
use Closure;

interface Middleware
{
    public function handle(Request $request, Closure $next): Response;
}
