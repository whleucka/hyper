<?php

namespace Nebula\Config;

class Database
{
    public array $config;
    public array $options;
    public function __construct()
    {
        $this->config = [
            "mode" => $_ENV["DB_MODE"],
            "dbname" => $_ENV["DB_NAME"],
            "host" => $_ENV["DB_HOST"],
            "port" => $_ENV["DB_PORT"],
            "username" => $_ENV["DB_USERNAME"],
            "password" => $_ENV["DB_PASSWORD"],
            "charset" => $_ENV["DB_CHARSET"],
        ];
        // Extra PDO options
        $this->options = [];
    }
}
