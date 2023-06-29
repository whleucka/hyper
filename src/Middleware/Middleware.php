<?php

namespace Nebula\Middleware;

use Symfony\Component\HttpFoundation\Request;

abstract class Middleware
{
    protected $next;

    public function setNext(Middleware $next): void
    {
        $this->next = $next;
    }
    /**
     * @return void
     */
    abstract public function handle(Request $request);
}
