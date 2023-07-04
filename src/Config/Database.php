<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

class Database
{
    private array $config;
    private array $options;
    public function __construct()
    {
        $env = Env::getInstance()->env();
        $this->config = [
            "enabled" => strtolower($env["DB_ENABLED"]) === "true",
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
