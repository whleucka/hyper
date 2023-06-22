<?php
/**
 * NEBULA -- a powerful PHP framework inspired by the cosmos
 * License: MIT
 * Created by: william.hleucka@gmail.com
 */

define('APP_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Util/functions.php';

app()->run();

//class TestController extends Nebula\Controllers\Controller
//{
//  public function index() { return "hello, world!"; }
//  public function test() { return "another route!"; }
//}
//
//app()
//  ->get("/", "TestController", "index")
//  ->post("/test", "TestController", "test")
//  ->run();
