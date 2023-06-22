<?php

use Nebula\Kernel\Web;

function dump($o)
{
    printf(
        "<pre
    style='overflow: auto; 
    padding: 20px; 
    background-color: #fbfbfb; 
    border: 2px dashed darkred;'>
<strong>DUMP</strong><br><br>
%s
    </pre>",
        print_r($o, true)
    );
}

function app()
{
    return Web::getInstance();
}

function twig($path, $data = [])
{
    $twig = app()
        ->container()
        ->get(Twig\Environment::class);
    return $twig->render($path, $data);
}
