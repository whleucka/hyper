<?php

namespace Nebula\Console;

use Nebula\Interfaces\Framework\Kernel as ConsoleKernel;
use Nebula\Interfaces\Http\Response;
use Throwable;

class Kernel implements ConsoleKernel
{
    public function handle(): Response
    {
        $response = app()->get(Response::class);
        $response->setContent("hello, world\n");
        return $response;
    }

    public function setup(): void
    {
    }

    public function handleException(Throwable $exception): Response
    {
        return "exception\n";
    }

    public function terminate(): void
    {
        logger('timeEnd', 'NebulaConsole');
        exit;
    }
}
