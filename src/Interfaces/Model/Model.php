<?php

namespace Nebula\Interfaces\Model;

interface Model 
{
  public static function find(mixed $id): ?self;
  public static function findByAttribute(string $attribute, mixed $value): ?self;
  public static function create(array $data): ?self;
  public function update(array $data);
  public function delete();
}
