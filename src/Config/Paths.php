<?php

namespace Nebula\Config;

class Paths
{
    private string $controllers = __DIR__ . "/../Controllers";

    public function getControllers(): string
    {
        return $this->controllers;
    }
}
