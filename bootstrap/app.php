<?php

use Nebula\Framework\Application;
use Dotenv;

/**
 * Instantiate the application
 */
$app = new Application;

// Register application singletons
$app->singleton(\Nebula\Interfaces\System\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Nebula\Interfaces\Http\Request::class, \Nebula\Http\Request::class);

$env_path = __DIR__ . "/../";
$dotenv = Dotenv\Dotenv::createImmutable($env_path);
$dotenv->load();

return $app;

