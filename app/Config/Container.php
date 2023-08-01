<?php

namespace App\Config;

use Nebula\Http\Kernel;
use Nebula\Http\Request;
use Nebula\UI\Twig\Extension;

return [
  /** Singletons **/
  \Nebula\Interfaces\Http\Kernel::class => Kernel::getInstance(),
  \Nebula\Interfaces\Framework\Environment::class => \Nebula\Framework\Environment::getInstance(),
  \Nebula\Interfaces\Http\Request::class => Request::getInstance(),
  \Nebula\Interfaces\Database\Database::class => function() {
    $db = \Nebula\Database\MySQLDatabase::getInstance();
    if (!$db->isConnected()) {
      $config = config("database");
      $db->connect($config);
    }
    return $db;
  },
  /** Non-Singletons **/
  \Nebula\Interfaces\Session\Session::class => \DI\get(\Nebula\Session\Session::class),
  \Nebula\Interfaces\Http\Response::class => \DI\get(\Nebula\Http\Response::class),
  \Nebula\Interfaces\Routing\Router::class => \DI\get(\Nebula\Routing\Router::class),
  \Twig\Environment::class => function () {
      $config = config("twig");
      $loader = new \Twig\Loader\FilesystemLoader($config["view_path"]);
      $twig = new \Twig\Environment($loader, [
        "cache" => $config["cache_path"],
        "auto_reload" => true,
      ]);
      $twig->addExtension(new Extension());
      return $twig;
  },
];
