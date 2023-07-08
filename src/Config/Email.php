<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

$env = Env::getInstance()->env();
return [
    "enabled" => strtolower($env["EMAIL_ENABLED"]) === "true",
    "debug" => strtolower($env["EMAIL_DEBUG"]) === "true",
    "host" => $env["EMAIL_HOST"],
    "port" => $env["EMAIL_PORT"],
    "username" => $env["EMAIL_USERNAME"],
    "password" => $env["EMAIL_PASSWORD"],
    "from_address" => $env["EMAIL_FROM"],
    "reply_to_address" => $env["EMAIL_REPLY_TO"],
];
