<?php

namespace Nebula\Kernel;

use GalaxyPDO\DB;
use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

class Web
{
  private DB $db;
  private $middleware;
  private Request $request;
  private array $config = [];

  /**
   * The application lifecycle
   */
  public function run(): void
  {
    $this->bootstrap();
    $this->loadMiddleware();
    $this->handleRequest();
    $this->routing();
    $this->payload();
    $this->handleExceptions();
    $this->response();
    $this->terminate();
  }

  /**
   * Set up essential components such as environment, configurations, db, etc
   */
  private function bootstrap(): void
  {
    $this->env();
    $this->config();
    $this->db();
  }

  /**
   * Load .env secrets
   */
  private function env(): void
  {
    $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
    // .env is required in the web root
    $dotenv->load();
  }

  /**
   * Load application configurations
   */
  private function config(): void
  {
    // Database configuration
    $this->config['db'] = new \Nebula\Config\Database;
  }

  /**
   * Initialize PDO
   */
  private function db(): void
  {
    $this->db = new DB($this->config['db']->config, $this->config['db']->options);
  }

  /**
   * Load the middleware and process incoming requests
   */
  private function loadMiddleware(): void
  {
    $this->middleware = [
      'session_start' => \Nebula\Middleware\Session\Start::class,
      'auth_user' => \Nebula\Middleware\Authentication\User::class,
    ];
  }

  /**
   * Handle in the incoming requests and send through middleware stack
   */
  private function handleRequest(): void
  {
    $request = Request::createFromGlobals();
    foreach ($this->middleware as $alias => $middleware) {
      $class = new $middleware;
      $request = match ($alias) {
        // We may define other match-arms to provide
        // additional arguments to handle here
        default => $class->handle($request)
      };
    }
    $this->request = $request;
  }

  /**
   * Route to the correct controller endpoint
   */
  private function routing(): void
  {
  }

  /**
   * Execute the controller method (controller interacts with models, prepares response)
   */
  private function payload(): void
  {
  }

  /**
   * Handle any errors / exceptions, logging, etc
   */
  private function handleExceptions(): void
  {
  }

  /**
   * Send the response to the client
   */
  private function response(): void
  {
  }

  /**
   * Terminate the request
   */
  private function terminate(): void
  {
  }
}
