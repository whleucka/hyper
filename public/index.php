<?php
/**
 * NEBULA -- a powerful PHP framework inspired by the cosmos
 * License: MIT
 * Created by: william.hleucka@gmail.com
 */

define('APP_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../src/Util/functions.php';

$app = new \Nebula\Kernel\Web();
$app->run();
