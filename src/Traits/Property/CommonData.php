<?php

namespace Nebula\Traits\Property;

trait CommonData
{
    /**
     * @param mixed $name
     */
    public function __get($name): mixed
    {
        return $this->get($name);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param array<int,mixed> $payload
     */
    public function load(array $payload): void
    {
        $this->data = $payload;
    }

    public function data(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $name
     */
    public function __isset($name): bool
    {
        return isset($this->data[$name]);
    }

    public function has(string $name): bool
    {
        return key_exists($name, $this->data);
    }

    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }

    public function get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }
}
