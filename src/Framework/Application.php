<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\System\Kernel;
use Nebula\Http\Request;
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
        $this->kernel->handleRequest($request)->send();
        $this->kernel->terminate();
    }

    public function use(): Kernel
    {
      return $this->kernel;
    }
}
