<?php

namespace App\Http;

use Nebula\Http\Kernel as HttpKernel;

final class Kernel extends HttpKernel
{
  // Register your application middleware classes
  protected array $middleware = [
    \Nebula\Middleware\Admin\Authentication::class, 
    \Nebula\Middleware\Http\Log::class, 
  ];
}
