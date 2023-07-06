<?php

namespace Nebula\Admin;

use Nebula\Models\User;
use Nebula\Validation\Validate;
use PragmaRX\Google2FA\Google2FA;
use stdClass;

class Auth
{
    public static function rememberMe(User $user): bool
    {
        $duration = time() + 30 * 24 * 60 * 60;
        // Generate a random token for the user
        $token = bin2hex(random_bytes(16));

        // Store the token in a cookie that expires after $duration
        setcookie("remember_token", $token, $duration, "/");

        // Store the token in the user row
        $user->remember_me = md5($token);
        return $user->update();
    }

    public static function twoFactorEnabled(): bool
    {
        $auth = new \Nebula\Config\Authentication();
        $config = $auth->getConfig();
        return $config["two_fa_enabled"];
    }

    public static function twoFactorSecret(User $user): bool
    {
        // Generate and save 2fa secret
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $user->two_fa_secret = $secret;
        return $user->update();
    }

    public static function getQR(User $user): string
    {
        $app = new \Nebula\Config\Application();
        $config = $app->getConfig();
        $name = $config["name"];

        $google2fa = new Google2FA();
        $text = $google2fa->getQRCodeUrl(
            $name,
            " $user->email",
            $user->two_fa_secret
        );

        $image_url =
            "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" .
            $text;
        return $image_url;
    }

    public static function validateTwoFactorCode(User $user, string $code): bool
    {
        $google2fa = new Google2FA();
        $result = $google2fa->verifyKey($user->two_fa_secret, $code);
        if (!$result) {
            self::failedAttempt($user);
        }
        return self::checkUserLock($user) && $result;
    }

    public static function authenticate(stdClass $data): ?User
    {
        $user = User::findByAttribute("email", $data->email);
        if ($user && self::checkUserLock($user)) {
            $result = password_verify($data->password, $user->password);
            if ($result) {
                return $user;
            }
            // Failed attempt
            self::failedAttempt($user);
        }
        return null;
    }

    public static function signOut(): void
    {
        self::destroyRememberCookie();
        session()->destroy();
        app()->redirect("auth.sign_in");
    }

    public static function destroyRememberCookie(): void
    {
        if (isset($_COOKIE["remember_token"])) {
            unset($_COOKIE["remember_token"]);
            setcookie("remember_token", "", -1, "/");
        }
    }

    public static function register(stdClass $data): ?User
    {
        $user = new User();
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = self::hashPassword($data->password);
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

    public static function signIn(User $user): void
    {
        if (!isset($_COOKIE["remember_token"])) {
            session()->set("user", $user->id);
        }
        self::clearReset($user);
        self::unlockAccount($user);
        $user->update();
        app()->redirect("admin.index");
    }

    public static function clearReset(User $user): bool
    {
        $user->reset_token = null;
        $user->reset_expires_at = null;
        return $user->update();
    }

    public static function failedAttempt(User $user): bool
    {
        if ($user->failed_login_attempts >= 10) {
            return true;
        }
        $user->failed_login_attempts++;
        return $user->update();
    }

    public static function lockAccount(User $user): bool
    {
        mailer()
            ->setSubject("Account locked")
            ->setTo($user->email)
            ->setTemplate("admin/auth/email/account-locked.html", [
                "name" => $user->name,
            ])
            ->send();
        $user->lock_expires_at = strtotime("+15 minute");
        return $user->update();
    }

    public static function unlockAccount(User $user): bool
    {
        $user->lock_expires_at = null;
        $user->failed_login_attempts = 0;
        return $user->update();
    }

    public static function checkUserLock(User $user): mixed
    {
        $valid = true;
        $time = time();
        if ($user->lock_expires_at) {
            if ($user->lock_expires_at > $time) {
                $valid &= false;
            } else {
                self::unlockAccount($user);
            }
        } elseif ($user->failed_login_attempts >= 10) {
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

    public static function forgotPassword(string $email): void
    {
        $user = User::findByAttribute("email", $email);
        if ($user) {
            $expires = strtotime("+ 15 minute");
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

    public static function hashPassword(string $password): string|bool
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    public static function validateForgotPassword(
        User $user,
        string $token
    ): bool {
        // Valid for 15 minutes
        return $user?->reset_token === $token &&
            $user?->reset_expires_at - time() > 0;
    }

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
