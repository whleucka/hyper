<?php

namespace App\Config;

return [
  'logging' => true,
  'name' => env("APP_NAME", "App"),
  'url' => env("APP_URL", "http://localhost"),
  'debug' => env("APP_DEBUG") == 'true',
];
