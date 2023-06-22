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

function get(
    $path,
    $handlerClass,
    $handlerMethod,
    string $name = "",
    array $middleware = []
) {
    $app = Web::getInstance();
    $app->router->registerRoute(
        $path,
        "GET",
        $name,
        $middleware,
        $handlerClass,
        $handlerMethod
    );
}

function post(
    $path,
    $handlerClass,
    $handlerMethod,
    string $name = "",
    array $middleware = []
) {
    $app = Web::getInstance();
    $app->router->registerRoute(
        $path,
        "POST",
        $name,
        $middleware,
        $handlerClass,
        $handlerMethod
    );
}

function put(
    $path,
    $handlerClass,
    $handlerMethod,
    string $name = "",
    array $middleware = []
) {
    $app = Web::getInstance();
    $app->router->registerRoute(
        $path,
        "PUT",
        $name,
        $middleware,
        $handlerClass,
        $handlerMethod
    );
}

function patch(
    $path,
    $handlerClass,
    $handlerMethod,
    string $name = "",
    array $middleware = []
) {
    $app = Web::getInstance();
    $app->router->registerRoute(
        $path,
        "PATCH",
        $name,
        $middleware,
        $handlerClass,
        $handlerMethod
    );
}

function delete(
    $path,
    $handlerClass,
    $handlerMethod,
    string $name = "",
    array $middleware = []
) {
    $app = Web::getInstance();
    $app->router->registerRoute(
        $path,
        "DELETE",
        $name,
        $middleware,
        $handlerClass,
        $handlerMethod
    );
}
