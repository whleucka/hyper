<?php

namespace App\Config;

return [
  "register_enabled" => env("AUTH_REGISTER_ENABLED", "true") == "true",
];
