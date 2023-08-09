<?php

namespace App\Config;

return [
  'register_enabled' => env("ADMIN_REGISTER_ENABLED", true) == 'true',
];
