<?php

namespace Nebula\Database;

use Closure;

class Schema
{
  /**
   * @param Closure(): void $callback
   */
  public static function create(string $table_name, Closure $callback): string
  {
    $blueprint = new Blueprint();
    $callback($blueprint);
    return sprintf(
      "CREATE TABLE IF NOT EXISTS %s (%s)",
      $table_name,
      $blueprint->getDefinitions()
    );
  }

  public static function drop(string $table_name): string
  {
    return sprintf("DROP TABLE IF EXISTS %s", $table_name);
  }

  public static function raw(string $query): string
  {
    return $query;
  }
}
