<?php
/**
 * NEBULA -- a powerful PHP framework inspired by the cosmos
 * Github: https://github.com/libra-php/nebula
 * Created: william.hleucka@gmail.com
 * License: MIT
 *
 *  🇨🇦 Made in Canada 
 */

require_once "bootstrap.php";

//app()->run();

class TestController extends Nebula\Controllers\Controller
{
  public function test(): int 
  { 
    return 42; 
  }
}

app()
  ->get("/", payload: fn() => "hello, world!")
  ->post("/test", "TestController", "test", middleware: ["api"])
  ->run();
