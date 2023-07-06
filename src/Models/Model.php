<?php

namespace Nebula\Models;

use Nebula\Container\Container;
use GalaxyPDO\DB;
use PDO;

class Model
{
    protected DB $db;
    protected Container $container;
    private string $table_name;
    private string $primary_key;
    private ?string $id;
    private bool $exists = false;
    private array $attributes = [];
    private array $public_properties = [];
    private array $protected_properties = [];
    private array $private_properties = [];

    public function __construct(
        string $table_name,
        string $primary_key,
        ?string $id = null
    ) {
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->id = $id;
        $this->loadAttributes();
    }

    /**
     * Find a model in the database
     */
    public static function find(mixed $id): ?Model
    {
        $class = static::class;
        $model = new $class($id);
        if ($model->exists()) {
            return $model;
        }
        return null;
    }

    /**
     * Find a model in the database by attribute
     */
    public static function findByAttribute(
        string $attribute,
        mixed $value
    ): ?Model {
        $class = static::class;
        $model = new $class();
        $id = db()->selectVar(
            "SELECT $model->primary_key 
            FROM $model->table_name 
            WHERE $attribute = ?",
            $value
        );
        if ($id) {
            return new $class($id);
        }
        return null;
    }

    /**
     * Load attributes and private/public properties
     */
    private function loadAttributes(): void
    {
        $class = static::class;
        $reflection = new \ReflectionClass($class);
        $public_properties = $reflection->getProperties(
            \ReflectionProperty::IS_PUBLIC
        );
        $protected_properties = $reflection->getProperties(
            \ReflectionProperty::IS_PROTECTED
        );
        $private_properties = $reflection->getProperties(
            \ReflectionProperty::IS_PRIVATE
        );
        if (!is_null($this->id)) {
            $row = db()->selectOne(
                "SELECT * FROM $this->table_name WHERE $this->primary_key = ?",
                $this->id
            );
            if ($row) {
                $this->exists = true;
            }
            foreach (
                [
                    ...$public_properties,
                    ...$protected_properties,
                    ...$private_properties,
                ]
                as $one
            ) {
                $property = $one->name;
                if ($row && property_exists($row, $property)) {
                    $this->attributes[$property] = $row->$property;
                }
            }
        } else {
            $desc = db()->query("DESCRIBE $this->table_name");
            $fields = $desc->fetchAll(PDO::FETCH_COLUMN);
            foreach ($fields as $field) {
                $this->attributes[$field] = null;
            }
        }
        $this->public_properties = array_map(
            fn($public) => $public->name,
            $public_properties
        );
        $this->protected_properties = array_map(
            fn($protected) => $protected->name,
            $protected_properties
        );
        $this->private_properties = array_map(
            fn($private) => $private->name,
            $private_properties
        );
        if ($this->exists()) {
            $this->fillProperties();
        }
    }

    /**
     * Fills all properties
     */
    public function fillProperties(): void
    {
        foreach (
            [
                $this->public_properties,
                $this->protected_properties,
                $this->private_properties,
            ]
            as $properties
        ) {
            foreach ($properties as $column) {
                if (property_exists($this, $column) && !isset($this->$column)) {
                    if (isset($this->attributes[$column])) {
                        $this->$column = $this->attributes[$column];
                    }
                }
            }
        }
    }

    /**
     * Formatted columns for query
     */
    public function getFormattedColumns(): string
    {
        $columns = array_filter(
            $this->public_properties,
            fn($property) => key_exists($property, $this->attributes)
        );
        $stmt = array_map(fn($column) => $column . " = ?", $columns);
        return implode(", ", $stmt);
    }

    /**
     * We only want public properties that exists as an entity attribute
     */
    public function attributeValues(): array
    {
        return array_map(
            fn($property) => $this->$property ?? null,
            array_filter(
                $this->public_properties,
                fn($property) => key_exists($property, $this->attributes)
            )
        );
    }

    /**
     * Insert model to database
     */
    public function insert(): ?Model
    {
        $columns = $this->getFormattedColumns();
        $values = $this->attributeValues();
        $result = db()->query(
            "INSERT INTO $this->table_name SET $columns",
            ...array_values($values)
        );
        if ($result) {
            $id = db()->lastInsertId();
            $class = static::class;
            return new $class($id);
        }
        return null;
    }

    /**
     * Update model in database
     */
    public function update(): bool
    {
        $columns = $this->getFormattedColumns();
        $values = $this->attributeValues();
        // Add the id to the values array as the last entry
        $values[] = $this->id;
        $result = db()->query(
            "UPDATE $this->table_name SET $columns WHERE $this->primary_key = ?",
            ...array_values($values)
        );
        if ($result) {
            $this->loadAttributes();
            return true;
        }
        return false;
    }

    /**
     * Delete model in database
     */
    public function delete(): bool
    {
        $result = db()->query(
            "DELETE FROM $this->table_name WHERE $this->primary_key = ?",
            $this->id
        );
        if ($result) {
            $this->loadAttributes();
            return true;
        }
        return false;
    }

    /**
     * Does this model exist in the database?
     */
    public function exists(): bool
    {
        return $this->exists === true;
    }

    /**
     * @param mixed $name
     */
    public function __get($name): mixed
    {
        return $this->attributes[$name];
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        // Only allow setting pulbic properties
        if (in_array($name, $this->public_properties)) {
            $this->attributes[$name] = $value;
        }
    }
}
