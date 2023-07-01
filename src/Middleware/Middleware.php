<?php

namespace Nebula\Middleware;

use Symfony\Component\HttpFoundation\Request;

abstract class Middleware
{
    abstract public function handle(Request $request): Request;
}
