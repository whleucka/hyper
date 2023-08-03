<?php
 // Instantiate the application
$app = app();

// Initialize the container
$app->build();

// Initialize logger
initLogger();

return $app;
