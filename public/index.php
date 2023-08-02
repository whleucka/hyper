<?php

/**
 * NEBULA -- a powerful PHP micro-framework
 * Github: https://github.com/libra-php/nebula
 * Created: william.hleucka@gmail.com
 * License: MIT
 */

define('APP_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';

logger('time', 'Nebula');
// Bootstrap & run the application
$app = require_once __DIR__ . "/../bootstrap/app.php";
$app->run(Nebula\Interfaces\Http\Kernel::class);
