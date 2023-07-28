<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\Http\Kernel;
use Nebula\Interfaces\Http\Request;
use Nebula\Traits\SingleInstance;

class Application extends Container
{
    use SingleInstance;

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
