<?php

namespace Nebula\Model;

use Closure;
use Nebula\Interfaces\Model\Model as NebulaModel;
use Nebula\Traits\Instance\Singleton;
use Nebula\Traits\Property\PrivateData;
use PDO;

class Model implements NebulaModel
{
  use Singleton;
  use PrivateData;

  private static function staticClass(): self
  {
    // Get the static class (e.g. User)
    $class = app()->get(static::class);  
    // Table name and primary key should be defined, otherwise throw error
    if (!property_exists($class, 'table_name')) {
      throw new \Error("table_name must be defined for " . static::class);
    }
    if (!property_exists($class, 'primary_key')) {
      throw new \Error("primary_key must be defined for " . static::class);
    }
    return $class;
  }

  private static function mapToString(array $data, Closure $fn, $separator = ", "): string
  {
    $columns = array_map($fn, $data);
    return implode($separator, $columns);
  }

  private static function values(array $data): array
  {
    return array_values($data);
  }

  public static function find(mixed $id): ?self
  {
    $model = app()->get(static::class);
    return self::findByAttribute($model->primary_key, $id);
  }

  public static function findByAttribute(string $attribute, mixed $value): ?self
  {
    $class = self::staticClass();
    // Build the sql query
    $sql = "SELECT * FROM $class->table_name WHERE $attribute = ?";
    // Select one item from the db
    $result = db()->run($sql, [$value])->fetch(PDO::FETCH_ASSOC);
    // Bail if it is bunk
    if (!$result) return null;
    // Create a model and load result
    $model = new $class($result[$class->primary_key]);
    $model->load($result);
    return $model;
  }

  public static function create(array $data): ?self
  {
    $class = self::staticClass();
    // Build the sql query
    $columns = self::mapToString(array_keys($data), fn($key) => "$key = ?");
    $values = self::values($data);
    $sql = "INSERT INTO $class->table_name SET $columns";
    $result = db()->run($sql, $values);
    if (!$result) return null;
    $id = db()->lastInsertId();
    return self::find($id);
  }

  public function update(array $data)
  {
  }

  public function delete()
  {
  }
}
