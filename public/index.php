<?php
/**
 * NEBULA -- a powerful PHP framework inspired by the cosmos
 * License: MIT
 * Created by: william.hleucka@gmail.com
 */

define('APP_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Util/functions.php';

//app()->run();

class TestController extends Nebula\Controllers\Controller
{
  public function test() { return "another route!"; }
}

app()
  ->get(path: "/", payload: fn() => "hello, world!")
  ->post("/test", "TestController", "test")
  ->run();
