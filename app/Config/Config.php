<?php

namespace App\Config;

final class Config
{
  public static function database()
  {
    return require __DIR__ . "/Database.php";
  }

  public static function twig()
  {
    return require __DIR__ . "/Twig.php";
  }

  public static function paths()
  {
    return require __DIR__ . "/Paths.php";
  }

  public static function container()
  {
    return require __DIR__ . "/Container.php";
  }
}
