<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

class Authentication
{
    private array $config;
    public function __construct()
    {
        $env = Env::getInstance()->env();
        $this->config = [
            "2fa_enabled" => strtolower($env["AUTH_2FA_ENABLED"]) === "true",
        ];
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
