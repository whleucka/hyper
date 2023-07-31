<?php

namespace Nebula\Framework;

use Nebula\Traits\Instance\Singleton;
use Dotenv\Dotenv;
use Nebula\Interfaces\Framework\Environment as FrameworkEnvironment;

class Environment implements FrameworkEnvironment
{
  use Singleton;

  private Dotenv $dotenv;

  public function __construct()
  {
    $config = config("paths");
    $this->dotenv = Dotenv::createImmutable($config['app_root']);
    $this->dotenv->safeLoad();
  }

  public function get(string $name): mixed
  {
    return isset($_ENV[$name])
        ? $_ENV[$name]
        : null;
  }
}
