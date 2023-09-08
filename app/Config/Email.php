<?php

namespace App\Config;

return [
  "enabled" => env("EMAIL_ENABLED", "true") == "true",
  "host" => env("EMAIL_HOST"),
  "port" => env("EMAIL_PORT"),
  "username" => env("EMAIL_USERNAME"),
  "password" => env("EMAIL_PASSWORD"),
  "from" => env("EMAIL_FROM"),
  "reply_to" => env("EMAIL_REPLY_TO"),
];
