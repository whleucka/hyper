<?php

use Nebula\Kernel\Web;
use Nebula\Validation\Validate;

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
    $line = $debug['line'];
    $file = $debug['file'];
    $function = $debug['function'];
    $dump = print_r($o ?? 'null', true);
    printf($template, $file.':'.$line, $function, $dump);
}

function dd($o)
{
    dump($o);
    die;
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
    $data['form_errors'] = Validate::$errors;
    dump($data);
    return $twig->render($path, $data);
}

function validate(array $request_data, array $rules)
{
    return Validate::request($request_data, $rules);
}
