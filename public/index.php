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
// However, you can still use traditional routing
// Delete the following line if you wish to use Controller
// Attribute-based-routing
// Example / web endpoint
$app->route('GET', '/', function() {
    return "Hello, world!";
}, middleware: ['cache=600']);
// Example /api/test api endpoint
$app->route('GET', '/api/test', function() {
    return [
        'success' => true,
        'code' => 200,
        'message' => 'Hello, world!'
    ];
}, middleware: ['api']);

$app->run(Nebula\Interfaces\Http\Kernel::class);
