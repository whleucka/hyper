<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\System\Kernel;
use Nebula\Http\Request;

class Application extends Container
{
  private Kernel $kernel;

  public function run(string $class): void
  {
    $this->kernel = $this->get($class);
    $request = $this->get(Request::class);
    $this->execute($request);
  }

  public function execute(Request $request): void
  {
      $this->kernel->handleRequest($request)->send();
      $this->kernel->terminate();
  }
}
