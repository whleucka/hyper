<?php

namespace Nebula\Kernel;

use Dotenv\Dotenv;
use Exception;

class Environment
{
    protected static $instance;
    private $env;

    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
            static::$instance->init();
        }

        return static::$instance;
    }

    public function env(): array
    {
        return $this->env;
    }

    public function init(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        // .env is required in the web root
        try {
            $dotenv->load();
            $this->env = $_ENV;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            die();
        }
    }
}
