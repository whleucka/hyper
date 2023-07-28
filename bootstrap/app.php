<?php

/**
 * Instantiate the application
 */
$app = app();

// Register application singletons
$app->singleton(\Nebula\Interfaces\Http\Kernel::class, \App\Http\Kernel::class);

return $app;

