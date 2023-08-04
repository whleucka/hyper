<?php

namespace Nebula\Model;

use Nebula\Interfaces\Database\Factory as NebulaFactory;

class Factory implements NebulaFactory
{
  protected string $model_class = Model::class;
}
