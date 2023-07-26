<?php

namespace App\Config;

return [
  "enabled" => $_ENV["DB_ENABLED"],
  "mode" => $_ENV['DB_MODE'],
  "name" => $_ENV['DB_NAME'],
  "host" => $_ENV['DB_HOST'],
  "port" => $_ENV['DB_PORT'],
  "username" => $_ENV['DB_USERNAME'],
  "password" => $_ENV['DB_PASSWORD'],
  "charset" => $_ENV['DB_CHARSET'],
];
