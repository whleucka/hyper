<?php

namespace Nebula\Http;

use Nebula\Interfaces\Http\Request as NebulaRequest;
use StellarRouter\Route;

class Request implements NebulaRequest
{
    public ?Route $route;

    public function getMethod(): string
    {
      return $_SERVER["REQUEST_METHOD"];
    }

    public function getUri(): string
    {
      return $_SERVER["REQUEST_URI"];
    }
}

