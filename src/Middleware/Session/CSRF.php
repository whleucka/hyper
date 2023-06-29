<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

class CSRF extends Middleware
{
    public function handle(Request $request): Middleware|Request
    {
        $this->init();
        $this->regenerate();

        // If authentication succeeds, call the next middleware
        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }

    /**
     * CSRF (Cross-Site Request Forgery) protection
     * Include the CSRF token as a hidden input field in your HTML forms.
     * This will ensure that the token is submitted along with the form data.
     */
    private function init(): void
    {
        if (!isset($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Validate CSRF token
     */
    private function validate(Request $request): ?Request
    {
        $valid = true;
        if ($request->getMethod() === "POST") {
            if (
                !empty($_POST["csrf_token"]) &&
                hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
            ) {
                // CSRF token is valid
                // Process the form submission
                $valid &= true;
            } else {
                // CSRF token is invalid
                // Handle the error, e.g., redirect or show an error message
                $valid &= false;
            }
        }
        return $valid ? $request : null;
    }

    /**
     * Regenerate CSRF token every hour
     */
    private function regenerate(): void
    {
        if (
            !isset($_SESSION["csrf_token_timestamp"]) ||
            $_SESSION["csrf_token_timestamp"] + 3600 < time()
        ) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            $_SESSION["csrf_token_timestamp"] = time();
        }
    }
}
