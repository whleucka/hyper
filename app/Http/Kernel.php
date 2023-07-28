<?php

namespace App\Http;

use Nebula\Http\Kernel as WebKernel;

final class Kernel extends WebKernel
{
  // Register your middleware classes here
  protected array $middleware = [
    \Nebula\Middleware\Admin\Authentication::class, 
    \Nebula\Middleware\Http\Log::class, 
  ];
}
