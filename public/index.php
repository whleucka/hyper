<?php
/**
 * NEBULA -- a powerful PHP micro-framework
 * Github: https://github.com/libra-php/nebula
 * Created: william.hleucka@gmail.com
 * License: MIT
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__ . "/../bootstrap/app.php";

// Attribute-based-routing is enabled by default
// However, you can also use the $app->route() method
// Delete the following line if you wish to use Controller
// Attribute-based-routing
$app->route('GET', '/', function() {
    return "Hello, world!";
}, middleware: ['cache=1440']);

$app->run(Nebula\Interfaces\Http\Kernel::class);
