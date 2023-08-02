<?php

namespace Nebula\Traits\Property;

trait PrivateData
{
  private array $data;

  /**
   * @param mixed $name
   */
  public function __get($name): mixed
  {
    return $this->data[$name] ?? null;
  }

  /**
   * @param mixed $name
   * @param mixed $value
   */
  public function __set($name, $value): void
  {
    $this->data[$name] = $value;
  }

  /**
   * @param mixed $name
   */
  public function __isset($name): bool
  {
    return isset($this->data[$name]);
  }

  public function get(string $name): mixed
  {
    return $this->data[$name] ?? null;
  }

  public function has(string $name): bool
  {
    return key_exists($name, $this->data);
  }

  public function set(string $name, mixed $value): void
  {
    if ($this->has($name)) {
      $this->data[$name] = $value;
    }
  }
}
