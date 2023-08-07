<?php

namespace App\Config;

return [
  'name' => env("APP_NAME", "App"),
  'url' => env("APP_URL", "http://localhost"),
  'debug' => env("APP_DEBUG") == 'true',
  'logging' => true,
];
