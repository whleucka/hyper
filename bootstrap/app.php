<?php

use Dotenv\Dotenv;

/**
 * Instantiate the application
 */
$app = app();

// Register application singletons
$app->singleton(\Nebula\Interfaces\System\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Nebula\Interfaces\Http\Request::class, \Nebula\Http\Request::class);

// Load environment variables
$env_path = __DIR__ . "/../";
$dotenv = Dotenv::createImmutable($env_path);
$dotenv->safeLoad();

return $app;

