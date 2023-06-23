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
app()->get("/",payload: fn()=>"hello, world!")->run();


