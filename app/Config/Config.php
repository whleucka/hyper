<?php

namespace App\Config;

final class Config
{
  public static function database()
  {
    return require __DIR__ . "/Database.php";
  }
}
