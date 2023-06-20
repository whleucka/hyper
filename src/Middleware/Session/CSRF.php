<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;

class CSRF
{
    /**
     * @param mixed $request
     */
    public function handle(Request $request): Request
    {
        $this->csrfProtection();
        $this->validateCSRFToken($request);
        return $request;
    }

    /**
     * CSRF (Cross-Site Request Forgery) protection
     */
    private function csrfProtection(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validateCSRFToken(Request $request): void
    {
        if ($request->getMethod() === 'POST') {
            // Validate CSRF token
            if (!empty($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // CSRF token is valid
                // Process the form submission
            } else {
                // CSRF token is invalid
                // Handle the error, e.g., redirect or show an error message
            }
        }
    }
}
