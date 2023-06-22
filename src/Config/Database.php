<?php

namespace Nebula\Config;

use Nebula\Kernel\Environment;

class Database
{
    private array $config;
    private array $options;
    public function __construct()
    {
        $env = Environment::getInstance()->env();
        $this->config = [
            "enabled" => $env["DB_ENABLED"],
            "mode" => $env["DB_MODE"],
            "dbname" => $env["DB_NAME"],
            "host" => $env["DB_HOST"],
            "port" => $env["DB_PORT"],
            "username" => $env["DB_USERNAME"],
            "password" => $env["DB_PASSWORD"],
            "charset" => $env["DB_CHARSET"],
        ];
        // Extra PDO options
        $this->options = [];
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
