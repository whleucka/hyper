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
// However, you can still register non-attribute-based 
$app->route('GET', '/hello/{var}', function($var) {
  return "Hello {$var}!";
}, middleware: ['cached']);

$app->run(Nebula\Interfaces\Http\Kernel::class);
