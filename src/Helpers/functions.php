<?php

use App\Models\User;
use Idearia\Logger;
use Nebula\Framework\Application;
use Nebula\Http\Request;
use Nebula\Interfaces\Database\Database;
use Nebula\Interfaces\Framework\Environment;
use Nebula\Interfaces\Session\Session;
use Nebula\Validation\Validate;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Mail\Email;

/**
 * This is a file that contains generic application functions
 * Do not add a namespace to this file
 */

/**
 * Dump args
 */
function dump(...$args)
{
    $out = array_map(fn($arg) => print_r($arg, true), $args);
    printf("<pre>%s</pre>", implode("\n\n", $out));
}

/**
 * Dump args and die
 */
function dd(...$args)
{
    dump(...$args);
    die();
}

/**
 * Get the middleware index of a given middleware name
 */
function middlewareIndex(array $middleware, string $name)
{
    foreach ($middleware as $key => $one) {
        if (preg_match("/$name/", $one)) {
            return $key;
        }
    }
}

/**
 * Generate a class map for the given directory
 * @return array<class-string,non-empty-string>
 */
function classMap(string $directory): array
{
    if (!file_exists($directory)) {
        throw new \Exception("class map directory doesn't exist");
    }
    return ClassMapGenerator::createMap($directory);
}

function redirect(string $url, int $code = 301, int $delay = 0): never
{
    logger("timeEnd", "Nebula");
    if ($delay > 0) {
        header("Refresh: $delay; URL=$url", response_code: $code);
    } else {
        header("Location: $url", response_code: $code);
    }
    exit();
}

function redirectRoute(string $name, int $code = 301, int $delay = 0)
{
    $route = app()
        ->use()
        ->router->findRouteByName($name);
    if ($route) {
        redirect($route->getPath(), $code, $delay);
    }
}

function initLogger()
{
    try {
        $log_path = config("paths.logs");
        $log_name = "nebula";
        $log_ext = "log";
        $log_file = $log_path . $log_name . "." . $log_ext;
        if (!file_exists($log_file)) {
            touch($log_file);
        }
        Logger::$write_log = true;
        Logger::$log_level = "debug";
        Logger::$log_dir = $log_path;
        Logger::$log_file_name = $log_name;
        Logger::$log_file_extension = $log_ext;
        Logger::$print_log = false;
    } catch (\Exception) {
    }
}

function logger(string $level, string $message, string $title = "")
{
    $enabled = config("app.logging");
    if ($enabled) {
        try {
            match ($level) {
                "time" => Logger::time($message),
                "timeEnd" => Logger::timeEnd($message),
                "debug" => Logger::debug($message, $title),
                "info" => Logger::info($message, $title),
                "warning" => Logger::warning($message, $title),
                "error" => Logger::error($message, $title),
                default => throw new \Exception("unknown log level"),
            };
        } catch (\Exception) {
        }
    }
}

/**
 * Return the SMTP emailer
 */
function emailer(): Email
{
    $emailer = app()->get(Email::class);
    $emailer->smtp();
    return $emailer;
}

/**
 * Return client IP
 */
function ip(): string
{
    if (!empty(request()->server()["HTTP_CLIENT_IP"])) {
        $ip = request()->server()["HTTP_CLIENT_IP"];
    } elseif (!empty(request()->server()["HTTP_X_FORWARDED_FOR"])) {
        $ip = request()->server()["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = request()->server()["REMOTE_ADDR"];
    }
    return $ip;
}

/**
 * Return the application singleton
 */
function app(): Application
{
    return Application::getInstance();
}

/**
 * Returns the application request singleton
 */
function request(): Request
{
    return app()->get(Request::class);
}

/**
 * Return the application configuration by name
 */
function config(string $name)
{
    $name_split = explode(".", $name);
    if (count($name_split) > 1) {
        $config = \Nebula\Config\Config::get($name_split[0]);
        return $config[$name_split[1]] ??
            throw new \Exception("Configuration item doesn't exist");
    }
    return \Nebula\Config\Config::get($name);
}

/**
 * Return the app user
 */
function user(): ?User
{
    $uuid = session()->get("user");
    if ($uuid) {
        $user = User::search(["uuid" => $uuid]);
        if ($user) {
            return $user;
        }
    }
    return null;
}

/**
 * Return the application environment variable by name
 */
function env(string $name, ?string $default = null)
{
    $env = app()->get(Environment::class);
    return $env->get($name) ?? $default;
}

/**
 * Return the application database
 */
function db(): Database
{
    return app()->get(Database::class);
}

function csrf(): string
{
    $token = session()->get("csrf_token");
    $input = <<<EOT
  <input type="hidden" name="csrf_token" value="$token">
EOT;
    return $input;
}

/**
 * Return the application session class
 */
function session(): Session
{
    return app()->get(Session::class);
}

/**
 * Return a twig rendered string
 */
function twig(string $path, array $data = []): string
{
    $twig = app()->get(\Twig\Environment::class);
    $form_errors = Validate::$errors;
    $data["form_errors"] = $form_errors;
    return $twig->render($path, $data);
}

/**
 * Return a latte rendered string
 */
function latte(string $path, array $data = [], ?string $block = null): string
{
    $latte = app()->get(\Latte\Engine::class);
    $form_errors = Validate::$errors;
    $data["form_errors"] = $form_errors;
    $data["form_error_keys"] = array_keys($form_errors);
    return $latte->renderToString($path, $data, $block);
}
