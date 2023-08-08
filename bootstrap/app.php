<?php
 // Instantiate the application
$app = app();

// Initialize the container
$app->build();
logger('time', 'Nebula');

// Initialize logger
initLogger();

// Register application routes
$app->route('GET', '/hello/{var}', function($var) {
  echo "Hello {$var}!";
}, middleware: ['cached']);

return $app;
