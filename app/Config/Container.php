<?php

namespace App\Config;

use Nebula\Http\Request;
use Nebula\UI\Twig\Extension;

return [
  \Nebula\Interfaces\Http\Kernel::class => \DI\get(\App\Http\Kernel::class),
  \Nebula\Interfaces\Session\Session::class => app()->getInstance(),
  \Nebula\Interfaces\Database\Database::class => \DI\get(\Nebula\Database\MySQLDatabase::class),
  \Nebula\Interfaces\Http\Request::class => Request::getInstance(),
  \Nebula\Interfaces\Http\Response::class => \DI\get(\Nebula\Http\Response::class),
  \Nebula\Interfaces\Routing\Router::class => \DI\get(\Nebula\Routing\Router::class),
  \Twig\Environment::class => function () {
      $config = app()->get(Config::class)::twig();
      $loader = new \Twig\Loader\FilesystemLoader($config["view_path"]);
      $twig = new \Twig\Environment($loader, [
        "cache" => $config["cache_path"],
        "auto_reload" => true,
      ]);
      $twig->addExtension(new Extension());
      return $twig;
  },
];
