<?php

namespace Nebula\Interfaces\Console;

interface Kernel
{
  public function handle(): void;
  public function setup(): void;
  public function handleException(\Throwable $exception): string;
  public function terminate(): void;
}
