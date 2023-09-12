<?php

namespace App\Config;

return [
  'logging' => true,
  'name' => env("APP_NAME", "App"),
  'url' => env("APP_URL", "http://127.0.0.1:8888"),
  'debug' => env("APP_DEBUG") == 'true',
];
