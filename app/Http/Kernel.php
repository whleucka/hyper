<?php

namespace App\Http;

use Nebula\Interfaces\Http\Request;
use Nebula\Http\Response;
use Nebula\Interfaces\System\Kernel as NebulaKernel;
use Throwable;

class Kernel implements NebulaKernel
{
    public function handleRequest(Request $request): Response
    {
      $response = new Response;
      $response->setStatusCode(200);
      $response->setContent("hello, world");
      return $response;
    }

    public function handleException(Throwable $exception): void
    {
    }

    public function terminate(): void
    {
      exit;
    }
}
