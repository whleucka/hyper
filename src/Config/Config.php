<?php

namespace Nebula\Config;

final class Config
{
  /**
   * Configurations are located in this namespace
   * TODO maybe use a classmap to load configurations
   */
  public static function get(string $name): mixed
  {
    if (self::exists($name)) {
      return require self::path($name);
    }
    throw new \Exception("Configuration doesn't exist");
  }

  /**
   * Convert config name to path
   */
  private static function path(string $name): string
  {
    $config_path = __DIR__ . "/../../app/Config/";
    return $config_path . ucfirst($name) . ".php";
  }

  /**
   * Does the file exists in \App\Config namespace?
   */
  private static function exists(string $name): bool
  {
    $filepath = self::path($name);
    return file_exists($filepath);
  }
}
