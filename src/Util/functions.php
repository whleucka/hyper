<?php

use Nebula\Kernel\Web;
use Nebula\Validation\Validate;
use Nebula\Util\TwigExtension;

function dump($o)
{
    $template = <<<PRE
<pre class='pre-debug'>
    <strong>DEBUG</strong><br>
    <small>File: %s</small><br>
    <small>Function: %s</small><br>
    <div style='padding-left: 32px;'>%s</div>
</pre>
PRE;
    $debug = debug_backtrace()[1];
    $line = $debug["line"];
    $file = $debug["file"];
    $function = $debug["function"];
    $dump = print_r($o ?? "null", true);
    printf($template, $file . ":" . $line, $function, $dump);
}

function dd($o)
{
    dump($o);
    die();
}

function uuid($data = null)
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    // Output the 36 character UUID.
    return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
}

function app()
{
    return Web::getInstance();
}

function request()
{
    return app()->getRequest();
}

function route()
{
    return app()->getRoute();
}

function twig($path, $data = [])
{
    $twig = app()
        ->container()
        ->get(Twig\Environment::class);
    $twig->addExtension(new TwigExtension());
    $data["form_errors"] = Validate::$errors;
    $data["csrf"] = function () {
        $token = $_SESSION["csrf_token"];
        $input = <<<EOT
<input type="hidden" name="csrf_token" value="{$token}">
EOT;
        echo $input;
    };
    return $twig->render($path, $data);
}

function validate(array $request_data, array $rules)
{
    return Validate::request($request_data, $rules);
}
