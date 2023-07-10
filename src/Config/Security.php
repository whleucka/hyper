<?php

namespace Nebula\Config;

return [
    // Session
    "session_lifetime_minutes" => 30,
    // Cookies
    "remember_me_expires" => time() + 30 * 24 * 60 * 60,
    // Rate limiting
    "rate_limit" => 120, // requests per window
    "window_size_minutes" => 5,
    // Failed login attempt
    "max_failed_login_attempts" => 10,
    // Account lock
    "lock_duration_minutes" => 15,
    // Forgot password
    "forgot_password_duration_minutes" => 15,
];
