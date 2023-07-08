<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

$env = Env::getInstance()->env();
return [
    "enabled" => strtolower($env["DB_ENABLED"]) === "true",
    "mode" => $env["DB_MODE"],
    "dbname" => $env["DB_NAME"],
    "host" => $env["DB_HOST"],
    "port" => $env["DB_PORT"],
    "username" => $env["DB_USERNAME"],
    "password" => $env["DB_PASSWORD"],
    "charset" => $env["DB_CHARSET"],
    "options" => [],
];
