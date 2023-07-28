<?php

namespace Nebula\Interfaces\Middleware;

use Closure;
use Nebula\Interfaces\Http\Request;

interface Middleware
{
    /**
     * @param Closure(): void $next
     */
    public function handle(Request $request, \Closure $next);
}
