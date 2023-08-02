<?php

use Idearia\Logger;
use Nebula\Framework\Application;
use Nebula\Http\Request;
use Nebula\Interfaces\Database\Database;
use Nebula\Interfaces\Framework\Environment;
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

function logger(string $level, string $message, string $title = '')
{
  if (Logger::$print_log) {
    $log_path = config('paths')['logs'];
    $log_name = "nebula";
    $log_ext = "log";
    $log_file = $log_path.$log_name.'.'.$log_ext;
    if (!file_exists($log_file)) {
      touch($log_file);
    }
    Logger::$write_log = true;
    Logger::$log_level = 'debug';
    Logger::$log_dir = $log_path;
    Logger::$log_file_name = $log_name;
    Logger::$log_file_extension = $log_ext;
    Logger::$print_log = false;
  }
  
  match ($level) {
    "time" => Logger::time($message),
    "timeEnd" => Logger::timeEnd($message),
    "debug" => Logger::debug($message, $title),
    "info" => Logger::info($message, $title),
    "warning" => Logger::warning($message, $title),
    "error" => Logger::error($message, $title),
    default => throw new \Exception("unknown log level"),
  };
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
  return app()->get(Request::class);
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
  $env = app()->get(Environment::class);
  return $env->get($name);
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
