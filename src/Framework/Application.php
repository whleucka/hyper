<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\Http\Kernel;
use Nebula\Interfaces\Http\Request;
use Nebula\Traits\Instance\Singleton;

class Application extends Container
{
    use Singleton;

    private Kernel $kernel;

    public function run(string $class): void
    {
        $this->kernel = $this->get($class);
        $this->kernel->setup($this);
        $this->execute();
    }

    public function execute(): void
    {
        $request = $this->get(Request::class);
        $response = $this->kernel->handleRequest($request);
        $response->send();
        $this->kernel->terminate();
    }

    public function use(): Kernel
    {
      return $this->kernel;
    }
}
