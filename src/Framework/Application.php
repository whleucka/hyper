<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Http\Kernel as HttpKernel;
use Nebula\Console\Kernel as ConsoleKernel;
use Nebula\Traits\Instance\Singleton;

class Application extends Container
{
    use Singleton;

    private HttpKernel|ConsoleKernel $kernel;

    public function run(string $class): void
    {
        $this->kernel = $this->get($class);
        $this->kernel->setup($this);
        $response = $this->kernel->handle();
        $response->send();
        $this->kernel->terminate();
    }

    public function use(): HttpKernel|ConsoleKernel
    {
      return $this->kernel;
    }
}
