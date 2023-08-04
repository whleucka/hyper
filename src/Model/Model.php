<?php

namespace Nebula\Model;

use Closure;
use Nebula\Interfaces\Model\Model as NebulaModel;
use Nebula\Traits\Property\ProtectedData;
use PDO;

class Model implements NebulaModel
{
  use ProtectedData;

  /**
   * Return the static class
   * @return self
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
   * @return string
   */
  private function mapToString(array $data, Closure $fn, $separator = ", "): string
  {
    $columns = array_map($fn, $data);
    return implode($separator, $columns);
  }

  /**
   * Get the insert query and values
   * @return array [sql, values]
   * @throws \Error if no data is present
   */
  private function getInsertQuery(): array
  {
    // Get the data from the model
    $data = $this->data();

    // Bail if there is no data
    if (!$data) {
      throw new \Error("No data to save");
    }

    // Build the sql query and insert to the database
    $columns = $this->mapToString(array_keys($data), fn ($key) => "$key = ?");
    $values = array_values($data);
    $sql = "INSERT INTO $this->table_name SET $columns";
    return [$sql, $values];
  }

  /**
   * Find a model from the database
   * @param array $data
   * @return self|null
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
    $model = self::staticClass();
    // Build the sql query
    $sql = "SELECT * FROM $model->table_name WHERE $attribute = ?";
    // Select one item from the db
    $result = db()->run($sql, [$value])->fetch(PDO::FETCH_ASSOC);
    // Bail if it is bunk
    if (!$result) {
      return null;
    }
    // Create a model and load result
    $model = new $model($result[$model->primary_key]);
    $model->load($result);
    return $model;
  }

  /**
   * Setup the model with initial table columns
   * @return void
   */
  private function setup(): void
  {
    // A new model will not have data, so we
    // will fill it in with table columns
    $table_columns = $this->getTableColumns($this->table_name);
    $diff_columns = array_diff($table_columns, $this->guarded);
    $update_columns = [];
    foreach ($diff_columns as $column) {
      $update_columns[$column] = $this->$column ?? null;
    }
    // Load the data into the model
    $this->load($update_columns);
  }

  public function refresh(): void
  {
    $model = self::find($this->id);
    $this->load($model->data());
  }

  /**
   * Create a new model
   * @return self|null
   */
  public function save(): ?self
  {
    $model = self::staticClass();
    $model->setup();
    list($sql, $values) = $model->getInsertQuery();
    $result = db()->run($sql, $values);
    if (!$result) {
      return null;
    }
    // Set the id and reload the model
    $model->id = db()->lastInsertId();
    // Refresh the model
    $model->refresh();
    return $model;
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
