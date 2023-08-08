<?php

namespace App\Config;

return [
  'enabled' => false,
  'scheme' => env("REDIS_SCHEME"),
  'host' => env('REDIS_HOST'),
  'port' => env('REDIS_PORT'),
  'requests_per' => 25,
  'rate_limit_seconds' => 60,
];
