<?php

namespace App\Config;

return [
  'enabed' => env("REDIS_ENABLED"),
  'scheme' => env("REDIS_SCHEME"),
  'host' => env('REDIS_HOST'),
  'port' => env('REDIS_PORT'),
];
