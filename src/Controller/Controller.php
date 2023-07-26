<?php

namespace Nebula\Controller;

use Nebula\Framework\Application;
use Nebula\Interfaces\Controller\Controller as BaseController;
use Nebula\Interfaces\Database\Database;
use App\Config\Config;

class Controller implements BaseController
{
  private Application $app;
  private Database $db;

  public function __construct(Application $application)
  {
    $this->app = $application;
    $this->db = $this->app->get(Database::class);
    $config = $this->app->get(Config::class)::database();
    $this->db->connect($config);
  }
}
