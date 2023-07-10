<?php

namespace Nebula\Admin;

use Nebula\Models\User;
use Nebula\Validation\Validate;
use PragmaRX\Google2FA\Google2FA;

class Auth
{
    /**
     * Set a remember me cookie for user
     */
    public static function rememberMe(User $user): bool
    {
        $expires = config("security")["remember_me_expires"];
        // Generate a random token for the user
        $token = bin2hex(random_bytes(16));

        // Store the token in a cookie that expires after $duration
        setcookie("remember_token", $token, $expires, "/");

        // Store the token in the user row
        $user->remember_token = md5($token);
        return $user->update();
    }

    /**
     * Is two factor enabled for the application?
     */
    public static function twoFactorEnabled(): bool
    {
        return config("auth")["two_fa_enabled"];
    }

    /**
     * Generate a 2FA secret for a user
     */
    public static function twoFactorSecret(User $user): bool
    {
        // Generate and save 2fa secret
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $user->two_fa_secret = $secret;
        return $user->update();
    }

    /**
     * Generate a 2FA QR code image link
     */
    public static function getQR(User $user): string
    {
        $name = config("app")["name"];

        $google2fa = new Google2FA();
        $text = $google2fa->getQRCodeUrl(
            $name,
            " $user->email",
            $user->two_fa_secret
        );

        return "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" .
            $text;
    }

    /**
     * Validate a user 2FA code
     */
    public static function validateTwoFactorCode(User $user, string $code): bool
    {
        $google2fa = new Google2FA();
        $result = $google2fa->verifyKey($user->two_fa_secret, $code);
        if (!$result) {
            self::failedAttempt($user);
        }
        return self::checkUserLock($user) && $result;
    }

    /**
     * Authenticate a user by email and password
     */
    public static function authenticate(): ?User
    {
        $user = User::findByAttribute("email", request()->get("email"));
        if ($user && self::checkUserLock($user)) {
            $result = password_verify(
                request()->get("password"),
                $user->password
            );
            if ($result) {
                return $user;
            }
            // Failed attempt
            self::failedAttempt($user);
        }
        return null;
    }

    /**
     * Sign out of application
     */
    public static function signOut(): void
    {
        self::destroyRememberCookie();
        session()->destroy();
        app()->redirect("auth.sign_in");
    }

    /**
     * Destroy a remember me token
     */
    public static function destroyRememberCookie(): void
    {
        if (isset($_COOKIE["remember_token"])) {
            unset($_COOKIE["remember_token"]);
            setcookie("remember_token", "", -1, "/");
        }
    }

    /**
     * Register a new user
     */
    public static function register(): ?User
    {
        $user = new User();
        $user->name = request()->get("name");
        $user->email = request()->get("email");
        $user->password = self::hashPassword(request()->get("password"));
        $user->failed_login_attempts = 0;
        $result = $user->insert();
        if ($result) {
            mailer()
                ->setSubject("New account created")
                ->setTo($user->email)
                ->setTemplate("admin/auth/email/new-registration.html", [
                    "name" => $user->name,
                    "email" => $user->email,
                ])
                ->send();
        }
        return $result;
    }

    /**
     * Sign in user and redirect to admin index
     */
    public static function signIn(User $user): void
    {
        session()->set("user", $user->getId());
        self::clearReset($user);
        self::unlockAccount($user);
        $user->update();
        app()->redirect("admin.index");
    }

    /**
     * Clear user password reset
     */
    public static function clearReset(User $user): bool
    {
        $user->reset_token = null;
        $user->reset_expires_at = null;
        return $user->update();
    }

    /**
     * Record a user failed login attempt
     */
    public static function failedAttempt(User $user): bool
    {
        $max_attempts = config("security")["max_failed_login_attempts"];
        if ($user->failed_login_attempts >= $max_attempts) {
            return true;
        }
        $user->failed_login_attempts++;
        return $user->update();
    }

    /**
     * Lock a user account for x minutes
     */
    public static function lockAccount(User $user): bool
    {
        $lock_mins = config("security")["lock_duration_minutes"];
        mailer()
            ->setSubject("Account locked")
            ->setTo($user->email)
            ->setTemplate("admin/auth/email/account-locked.html", [
                "name" => $user->name,
            ])
            ->send();
        $user->lock_expires_at = strtotime("+$lock_mins minute");
        return $user->update();
    }

    /**
     * Unlock a user account
     */
    public static function unlockAccount(User $user): bool
    {
        $user->lock_expires_at = null;
        $user->failed_login_attempts = 0;
        return $user->update();
    }

    /**
     * Check for a user lock (locks and unlocks user if necessary)
     */
    public static function checkUserLock(User $user): mixed
    {
        $valid = true;
        $time = time();
        $max_attempts = config("security")["max_failed_login_attempts"];
        if ($user->lock_expires_at) {
            if ($user->lock_expires_at > $time) {
                $valid &= false;
            } else {
                self::unlockAccount($user);
            }
        } elseif ($user->failed_login_attempts >= $max_attempts) {
            self::lockAccount($user);
            $valid &= false;
        }
        if (!$valid) {
            // Set validation error messages for email and code
            $remaining = gmdate("i:s", $user->lock_expires_at - $time);
            Validate::addError(
                "email",
                "This account is locked. Time remaining: $remaining"
            );
            Validate::addError(
                "code",
                "This account is locked. Time remaining: $remaining"
            );
        }
        return $valid;
    }

    /**
     * Generate a random 32-character token
     */
    public static function generateToken(): string
    {
        $length = 32;
        $characters =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $token = "";

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $token;
    }

    /**
     * Issue user a password reset link via email
     */
    public static function forgotPassword(string $email): void
    {
        $forgot_pass_mins = config("security")["forgot_password_duration_minutes"];
        $user = User::findByAttribute("email", $email);
        if ($user) {
            $expires = strtotime("+$forgot_pass_mins minute");
            $token = self::generateToken();
            $user->reset_token = $token;
            $user->reset_expires_at = $expires;
            $user->update();
            $password_reset_route = app()->buildRoute(
                "auth.password_reset",
                $user->uuid,
                $token
            );
            $url = app()->routePathURL($password_reset_route);
            mailer()
                ->setSubject("Password reset requested")
                ->setTo($user->email)
                ->setTemplate("admin/auth/email/forgot-password.html", [
                    "name" => $user->name,
                    "url" => $url,
                ])
                ->send();
        }
    }

    /**
     * Hash a user password
     */
    public static function hashPassword(string $password): string|bool
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    /**
     * Validate a user's forgot password token
     */
    public static function validateForgotPassword(
        User $user,
        string $token
    ): bool {
        // Valid for 15 minutes
        return $user?->reset_token === $token &&
            $user?->reset_expires_at - time() > 0;
    }

    /**
     * Change a user's password
     */
    public static function changePassword(User $user, string $password): bool
    {
        $user->password = self::hashPassword($password);
        $result = $user->update();
        if ($result) {
            mailer()
                ->setSubject("Password changed successfully")
                ->setTo($user->email)
                ->setTemplate("admin/auth/email/password-change.html", [
                    "name" => $user->name,
                ])
                ->send();
        }
        return $result;
    }
}
