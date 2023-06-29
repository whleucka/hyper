<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

class CSRF extends Middleware
{
    /**
     * CSRF (Cross-Site Request Forgery) protection
     */
    public function handle(Request $request): Middleware|Request
    {
        $this->init();
        $this->regenerate();
        $request = $this->validate($request);

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }

    /*
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
        if ($request->getMethod() === "POST") {
            if (
                !empty($request->get("csrf_token")) &&
                hash_equals(
                    $_SESSION["csrf_token"],
                    $request->get("csrf_token")
                )
            ) {
                // CSRF token is valid
                return $request;
            } else {
                // CSRF token is invalid
                app()->forbidden();
            }
        }
        return $request;
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
