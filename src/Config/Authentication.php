<?php

namespace Nebula\Config;

use Nebula\Kernel\Env;

$env = Env::getInstance()->env();
return [
    "two_fa_enabled" => strtolower($env["AUTH_2FA_ENABLED"]) === "true",
];
