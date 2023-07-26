<?php

use Nebula\Framework\Application;

/**
 * Instantiate the application
 */
$app = new Application;

// Register application singletons
$app->singleton(\Nebula\Interfaces\System\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Nebula\Interfaces\Http\Request::class, \Nebula\Http\Request::class);

return $app;

