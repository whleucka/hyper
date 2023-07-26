<?php

namespace App\Http;

use Nebula\Framework\Application;
use Nebula\Interfaces\Http\Request;
use Nebula\Interfaces\Http\Response;
use Nebula\Interfaces\System\Kernel as NebulaKernel;
use Throwable;

class Kernel implements NebulaKernel
{
    private Application $app;

    /**
     * Setup the application
     */
    public function setup(Application $app): Kernel
    {
        $this->app = $app;
        $this->registerInterfaces();
        return $this;
    }

    /**
     * Register all default the framework interface binding
     */
    public function registerInterfaces(): void
    {
        $this->app->bind(\Nebula\Interfaces\Http\Response::class, \Nebula\Http\Response::class);
    }

    /**
     * Handle the request and return a response
     */
    public function handleRequest(Request $request): Response
    {
        $response = $this->app->get(Response::class);
        $response->setStatusCode(200);
        $response->setContent("hello, world");
        return $response;
    }

    /**
     * Handle any application exceptions
     */
    public function handleException(Throwable $exception): void
    {
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    {
        exit;
    }
}
