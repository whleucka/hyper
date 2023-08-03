<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\Framework\Kernel;
use Nebula\Traits\Instance\Singleton;

class Application extends Container
{
    use Singleton;

    private Kernel $kernel;

    public function run(string $class): void
    {
        $this->kernel = $this->get($class);
        $this->kernel->setup($this);
        $response = $this->kernel->handle();
        $response->send();
        $this->kernel->terminate();
    }

    public function use(): Kernel
    {
      return $this->kernel;
    }
}
