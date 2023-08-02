<?php

namespace Nebula\Interfaces\Http;

interface Kernel
{
  public function handle(): Response;
  public function setup(): void;
  public function handleException(\Throwable $exception): Response;
  public function terminate(): void;
}
