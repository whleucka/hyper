<?php

namespace Nebula\Kernel;

use GalaxyPDO\DB;
use Dotenv\Dotenv;

class Web
{
  private DB $db;
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
  }

  /**
   * Handle in the incoming requests and send through middleware stack
   */
  private function handleRequest(): void
  {
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
