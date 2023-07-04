<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

class Email
{
    private array $config;
    public function __construct()
    {
        $env = Env::getInstance()->env();
        $this->config = [
            "enabled" => strtolower($env["EMAIL_ENABLED"]) === 'true',
            "debug" => strtolower($env["EMAIL_DEBUG"]) === 'true',
            "host" => $env["EMAIL_HOST"],
            "port" => $env["EMAIL_PORT"],
            "username" => $env["EMAIL_USERNAME"],
            "password" => $env["EMAIL_PASSWORD"],
            "from_address" => $env["EMAIL_FROM"],
            "reply_to_address" => $env["EMAIL_REPLY_TO"],
        ];
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
