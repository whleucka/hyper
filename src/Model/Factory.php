<?php

namespace Nebula\Model;

use Nebula\Interfaces\Database\Factory as NebulaFactory;

class Factory implements NebulaFactory
{
  protected string $model = Model::class;

  /**
   * @param array<int,mixed> $data
   */
  public function make(?array $data = null, int $n = 1, bool $save = true): mixed
  {
    $mock = empty($data) || is_null($data);
    $models = [];
    for ($i = 0; $i < $n; $i++) {
      $model = $this->new($data, $mock);
      if ($save) {
        $model->save();
      }
      $models[] = $model;
    }
    return $models;
  }

  /**
   * @param array<int,mixed> $data
   */
  public function new(?array $data, bool $mock = false): Model
  {
    $model = new $this->model();
    if ($mock) {
      $data = $this->mock();
    } else if (is_null($data)) {
      $data = [];
    }
    foreach ($data as $key => $value) {
      $model->$key = $value;
    }
    return $model;
  }

  public function mock(): array
  {
    return [];
  }
}
