<?php

/**
 * NEBULA -- a powerful PHP micro-framework
 * Github: https://github.com/libra-php/nebula
 * Created: william.hleucka@gmail.com
 * License: MIT
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__ . "/../bootstrap/app.php";
$app->run(Nebula\Interfaces\Http\Kernel::class);
