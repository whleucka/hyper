<?php
/**
 * NEBULA -- a powerful PHP framework
 * Github: https://github.com/libra-php/nebula
 * Created: william.hleucka@gmail.com
 * License: MIT
 */
require_once "bootstrap.php";

// Run the app using attribute-based routing
app()->run();

// Or, you can define the routes and call run
//app()->get("/", payload: fn() => "hello, world")->run();
