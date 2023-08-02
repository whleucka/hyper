<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Traits\Instance\Singleton;

class Application extends Container
{
    use Singleton;

    private $kernel;

    public function run(string $class): void
    {
        $this->kernel = $this->get($class);
        $this->kernel->setup($this);
        $this->kernel->handle();
        $this->kernel->terminate();
    }

    public function use(): Kernel
    {
      return $this->kernel;
    }
}
