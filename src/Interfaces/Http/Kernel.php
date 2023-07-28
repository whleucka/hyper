<?php

namespace Nebula\Interfaces\Http;

interface Kernel
{
  public function handleRequest(Request $request): Response;
  public function handleException(\Throwable $exception): Response;
  public function terminate(): void;
}
