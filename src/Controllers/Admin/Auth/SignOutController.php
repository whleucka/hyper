<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use StellarRouter\Get;

class SignOutController extends Controller
{
    #[Get("/admin/sign-out", "auth.sign_out")]
    public function sign_out(): void
    {
        Auth::signOut();
    }
}
