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
}
