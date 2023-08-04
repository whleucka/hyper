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
    return self::search([$model->primary_key => $id]);
  }

  /**
   * Find a model by an attribute
   */
  public static function search(array $where, string $operator = "=", ?int $limit = null): mixed
  {
    $model = self::staticClass();
    // Build the sql query
    $columns = implode(', ', $model->getTableColumns($model->table_name));
    $where_clause = $model->mapToString(array_keys($where), fn ($key) => "$key $operator ?", ", ");
    $sql = "SELECT $columns FROM $model->table_name WHERE $where_clause";
    if (!is_null($limit)) {
      $sql .= " LIMIT $limit";
    }
    $values = array_values($where);
    // Select one item from the db
    $result = db()->run($sql, $values)->fetchAll(PDO::FETCH_ASSOC);
    // Bail if it is bunk
    if (!$result) {
      return null;
    }
    if (count($result) ===  1) {
      $result = $result[0];  
      // Create a model and load result
      $model = new $model($result[$model->primary_key]);
      $model->load($result);
      return $model;
    }
    // If there are multiple results, return an array of models
    $models = [];
    foreach ($result as $data) {
      $model = new $model($data[$model->primary_key]);
      $model->load($data);
      $models[] = $model;
    }
    return $models;
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
