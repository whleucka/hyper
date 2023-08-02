<?php

namespace Nebula\Console;

use Nebula\Interfaces\Console\Kernel as ConsoleKernel;
use Throwable;

class Kernel implements ConsoleKernel
{
    public function handle(): void
    {
        echo "hello, world\n";
    }

    public function setup(): void
    {
    }

    public function handleException(Throwable $exception): string
    {
        return "exception\n";
    }

    public function terminate(): void
    {
        exit;
    }
}
