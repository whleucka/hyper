<?php

namespace Nebula\Middleware\Authentication;

use Symfony\Component\HttpFoundation\Request;

class User
{
    /**
     * @param mixed $request
     */
    public function handle(Request $request): Request
    {
        return $request;
    }
}
