<?php

use Nebula\Framework\Application;
use Nebula\Http\Request;
use Nebula\Interfaces\Database\Database;
use Nebula\Interfaces\Session\Session;

/**
 * This is a file that contains generic application functions
 * Do not add a namespace to this file
 */

/**
 * Dump args
 */
function dump(...$args)
{
  $out = array_map(fn ($arg) => print_r($arg, true), $args);
  printf("<pre>%s</pre>", implode("\n\n", $out));
}

/**
 * Dump args and die
 */
function dd(...$args)
{
  dump(...$args);
  die;
}

/**
 * Return the application singleton
 */
function app()
{
  return Application::getInstance();
}

/**
 * Returns the application request singleton
 */
function request()
{
  return Request::getInstance();
}

/**
 * Return the application configuration by name
 */
function config(string $name)
{
  return \App\Config\Config::get($name);
}

/**
 * Return the application environment variable by name
 */
function env(string $name)
{
  return app()->use()->getEnvironment($name);
}

/**
 * Return the application database
 */
function db()
{
  return app()->get(Database::class);
}

/**
 * Return the application session class
 */
function session()
{
  return app()->get(Session::class);
}

/**
 * Return a twig rendered string
 */
function twig(string $path, array $data = []): string
{
  $twig = app()->get(\Twig\Environment::class);
  return $twig->render($path, $data);
}
