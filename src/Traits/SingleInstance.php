<?php

namespace Nebula\Traits;

/**
 * Singleton trait
 */
trait SingleInstance
{
    public static $instance;
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    protected function __construct()
    {
    }

    private function __clone(): void
    {
    }
}
