<?php

namespace Nebula\Interfaces\Model;

interface Model 
{
  public static function find(mixed $id): ?self;
  public static function findByAttribute(string $attribute, mixed $value): ?self;
  public function save(): ?self;
  public function update(): void;
  public function refresh(): void;
  public function delete(): void;
}
