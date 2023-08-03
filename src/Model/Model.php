<?php

namespace Nebula\Model;

use Nebula\Interfaces\Model\Model as NebulaModel;
use Nebula\Traits\Instance\Singleton;
use Nebula\Traits\Property\PrivateData;
use PDO;

class Model implements NebulaModel
{
  use Singleton;
  use PrivateData;

  public static function find(mixed $id): ?self
  {
    $model = app()->get(static::class);
    return self::findByAttribute($model->primary_key, $id);
  }

  public static function findByAttribute(string $attribute, mixed $value): ?self
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

  public function create()
  {
  }

  public function update()
  {
  }

  public function delete()
  {
  }
}
