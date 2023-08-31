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
// Delete the following routes if you wish to use Controller
// Attribute-based-routing
// Example / web endpoint
$app->route('GET', '/', function() {
    $count = session()->get('count') ?? 0;
    return twig('welcome/index.html', ['count' => $count]);
});
// Example /api/test api endpoint
$app->route('POST', '/api/count', function() {
    $count = session()->get('count');
    $count++;
    session()->set('count', $count);
    return $count;
}, middleware: ['api']);

$app->run(Nebula\Interfaces\Http\Kernel::class);
