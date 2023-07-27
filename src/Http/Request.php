<?php

namespace Nebula\Http;

use Nebula\Interfaces\Http\Request as NebulaRequest;

class Request implements NebulaRequest
{
    public function getMethod(): string
    {
      return $_SERVER["REQUEST_METHOD"];
    }

    public function getUri(): string
    {
      return $_SERVER["REQUEST_URI"];
    }
}

