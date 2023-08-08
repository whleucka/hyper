<?php
 // Instantiate the application
$app = app();

// Initialize the container
$app->build();
logger('time', 'Nebula');

// Initialize logger
initLogger();

return $app;
