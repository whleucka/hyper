<?php

namespace Nebula\Interfaces\Framework;

use Nebula\Interfaces\Http\Response;

interface Kernel
{
    public function handle(): Response;
    public function setup(): void;
    public function handleException(\Throwable $exception): Response;
    public function terminate(): never;
}
