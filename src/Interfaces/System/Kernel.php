<?php

namespace Nebula\Interfaces\System;

use Nebula\Interfaces\Http\Request;
use Nebula\Interfaces\Http\Response;

interface Kernel
{
  public function handleRequest(Request $request): Response;
  public function handleException(\Throwable $exception): void;
  public function terminate(): void;
}
