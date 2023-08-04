<?php

namespace Nebula\Model;

use Closure;
use Nebula\Interfaces\Model\Model as NebulaModel;
use Nebula\Traits\Instance\Singleton;
use Nebula\Traits\Property\ProtectedData;
use PDO;

class Model implements NebulaModel
{
  use Singleton;
  use ProtectedData;

  /**
   * Return the static class
   * @throws \Error if table_name or primary_key is not defined
   */
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

  /**
   * Get the table columns
   * @return array
   */
  private function getTableColumns(string $table_name): array
  {
    return db()->query("DESCRIBE $table_name")->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * Load data into the model
   * @param array $data
   * @param Closure(): void $fn
   * @param mixed $separator
   * @return void
   */
  private static function mapToString(array $data, Closure $fn, $separator = ", "): string
  {
    $columns = array_map($fn, $data);
    return implode($separator, $columns);
  }

  /**
   * Get the values from an array
   * @param array $data
   * @return array
   */
  private static function values(array $data): array
  {
    return array_values($data);
  }

  /**
   * Find a model from the database
   * @param array $data
   * @return array
   */
  public static function find(mixed $id): ?self
  {
    $model = app()->get(static::class);
    return self::findByAttribute($model->primary_key, $id);
  }

  /**
   * Find a model by an attribute
   * @param string $attribute
   * @param mixed $value
   * @return self|null
   */
  public static function findByAttribute(string $attribute, mixed $value): ?self
  {
    $class = self::staticClass();
    // Build the sql query
    $sql = "SELECT * FROM $class->table_name WHERE $attribute = ?";
    // Select one item from the db
    $result = db()->run($sql, [$value])->fetch(PDO::FETCH_ASSOC);
    // Bail if it is bunk
    if (!$result) {
      return null;
    }
    // Create a model and load result
    $model = new $class($result[$class->primary_key]);
    $model->load($result);
    return $model;
  }

  /**
   * Create a new model
   * @throws \Error if no data is present
   */
  public function save(): ?self
  {
    $class = self::staticClass();

    // A new model will not have data, so we will fill it in
    $table_columns = $this->getTableColumns($class->table_name);
    $diff_columns = array_diff($table_columns, $class->guarded);
    $update_columns = [];
    foreach ($diff_columns as $column) {
      $update_columns[$column] = $class->$column ?? null;
    }
    $class->load($update_columns);
    
    // Get the data from the model 
    $data = $class->data();
    if (!$data) throw new \Error("No data to save");

    // Build the sql query and insert to the database
    $columns = self::mapToString(array_keys($data), fn ($key) => "$key = ?");
    $values = self::values($data);
    $sql = "INSERT INTO $class->table_name SET $columns";
    $result = db()->run($sql, $values);
    if (!$result) {
      return null;
    }
    $id = db()->lastInsertId();
    return $class::find($id);
  }

  /**
   * Update a model
   */
  public function update(): void
  {
  }

  /**
   * Delete a model
   */
  public function delete(): void
  {
  }
}
