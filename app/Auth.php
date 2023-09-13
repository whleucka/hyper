<?php

namespace App;

use App\Models\User;
use Sonata\GoogleAuthenticator\{GoogleQrUrl, GoogleAuthenticator};

class Auth
{
  public static function hashPassword(string $password): string
  {
    return password_hash($password, PASSWORD_ARGON2I);
  }

  public static function validatePassword(User $user, string $password): bool
  {
    return password_verify($password, $user->password);
  }

  public static function signIn(User $user)
  {
      session()->set("user", $user->uuid);
      return redirectRoute("dashboard.index");
  }

  public static function twoFactorAuthentication(User $user)
  {
      session()->set("two_fa", $user->uuid);
      return redirectRoute("two-factor-authentication.index");
  }

  public static function twoFactorRegister(User $user)
  {
      session()->set("two_fa", $user->uuid);
      return redirectRoute("two-factor-register.index");
  }

  public static function urlQR(User $user): string
  {
    return GoogleQrUrl::generate($user->email, $user->two_fa_secret, config("app.name"));
  }


  public static function generateTwoFASecret(): string
  {
    $g = new GoogleAuthenticator();
    return $g->generateSecret();
  }

  public static function validateCode(string $secret, string $code): bool
  {
    $g = new GoogleAuthenticator();
    return $g->checkCode($secret, $code);
  }
}
