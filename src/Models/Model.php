<?php

namespace Nebula\Models;

use Nebula\Container\Container;
use GalaxyPDO\DB;

class Model
{
    protected DB $db;
    protected Container $container;
    private string $table_name;
    private string $primary_key;
    private ?string $id;
    private bool $loaded = false;
    private array $attributes = [];

    public function __construct(
        string $table_name,
        string $primary_key,
        ?string $id = null
    ) {
        $this->container = Container::getInstance();
        $this->db = $this->container->get(DB::class);
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->id = $id;
        $this->loadAttributes();
    }

    private function loadAttributes(): void
    {
        $class = static::class;
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties(
            \ReflectionProperty::IS_PRIVATE
        );
        if (!is_null($this->id)) {
            $row = $this->db->selectOne(
                "SELECT * FROM $this->table_name WHERE $this->primary_key = ?",
                $this->id
            );
            if ($row) {
                $this->loaded = true;
            }
        }
        foreach ($properties as $one) {
            $property = $one->name;
            $this->attributes[$property] = $row?->$property ?? null;
        }
    }

    public function isLoaded(): bool
    {
        return $this->loaded === true;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
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
        $this->attributes[$name] = $value;
    }
}
