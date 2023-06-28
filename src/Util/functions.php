<?php

use Nebula\Kernel\Web;
use Nebula\Validation\Validate;

function dump($o)
{
    $template = <<<PRE
<pre class='pre-debug'>
<strong>DEBUG</strong><br><br>
%s
</pre>
PRE;
    printf($template, print_r($o, true));
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
