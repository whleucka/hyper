<?php

namespace Nebula\Interfaces\Session;

interface Session
{
  public function get(string $name): mixed;
  public function set(string $name, mixed $value): void;
  public function getAll(): array;
  public function destroy(): void;
}
