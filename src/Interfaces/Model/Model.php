<?php

namespace Nebula\Interfaces\Model;

interface Model 
{
  public static function find(mixed $id);
  public static function findByAttribute(string $attribute, mixed $value): ?self;
  public function create(array $data);
  public function update(array $data);
  public function delete();
}
