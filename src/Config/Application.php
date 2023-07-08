<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

$env = Env::getInstance()->env();
return [
    "name" => $env["APP_NAME"],
    "url" => $env["APP_URL"],
    "debug" => strtolower($env["APP_DEBUG"]) === "true",
];
