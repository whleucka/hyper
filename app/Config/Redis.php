<?php

namespace App\Config;

return [
    // Redis configuration
    "enabled" => env("REDIS_ENABLED", "false") == "true",
    "scheme" => env("REDIS_SCHEME"),
    "host" => env("REDIS_HOST"),
    "port" => env("REDIS_PORT"),
    // Request rate limiting configuration
    "requests_per_second" => 25,
    "rps_window_seconds" => 60,
    // Cache configuration
    "cache_default_ttl" => 60 * 15, // 15 minutes
];
