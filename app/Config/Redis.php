<?php

namespace App\Config;

return [
  // Redis configuration
  'enabled' => env("REDIS_ENABLED", false),
  'scheme' => env("REDIS_SCHEME"),
  'host' => env('REDIS_HOST'),
  'port' => env('REDIS_PORT'),
  // Request rate limiting configuration
  'requests_per_second' => 25,
  'rps_window_seconds' => 60,
];
