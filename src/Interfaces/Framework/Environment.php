<?php

namespace Nebula\Interfaces\Framework;

interface Environment
{
  public function get(string $name): mixed;
}
