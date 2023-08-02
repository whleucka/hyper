<?php

namespace App\Http;

use Nebula\Http\Kernel as HttpKernel;

final class Kernel extends HttpKernel
{
  // Register your middleware classes here
  protected array $middleware = [
    \Nebula\Middleware\Admin\Authentication::class, 
    \Nebula\Middleware\Http\Log::class, 
  ];
}
