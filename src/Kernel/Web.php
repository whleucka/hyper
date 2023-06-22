<?php

namespace Nebula\Kernel;

use Nebula\Container\Container;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Controllers\Controller;
use StellarRouter\Route;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\Request;

class Web
{
  private ?Route $route;
  private Router $router;
  private Container $container;
  private Controller $controller;

  public function run(): void
  {
    $this->buildContainer();
    $this->setupRoutes();
    $this->routing();
    $this->controller();
  }

  /**
   * Route via method attributes
   */
  public function setupRoutes(): void
  {
    // Controller path via getControllers
    $this->router = $this->container->get(Router::class);
    $config = new \Nebula\Config\Paths();
    $controllers = array_keys(
      $this->classMap($config->getControllers())
    );
    if ($controllers) {
      foreach ($controllers as $controllerClass) {
        $controller = $this->container->get($controllerClass);
        $this->router->registerRoutes($controller::class);
      }
    }
  }

  /**
   * @return array<class-string,non-empty-string>
   */
  private function classMap(string $path): array
  {
    return ClassMapGenerator::createMap($path);
  }

  /**
   * Setup DI container
   */
  public function buildContainer(): void
  {
    $this->container = Container::getInstance();
    $config = new \Nebula\Config\Container();
    $this->container
      ->setDefinitions($config->getDefinitions())
      ->build();
  }

  public function routing(): void
  {
      $request = Request::createFromGlobals();
      // Set the route
      $this->route = $this->router->handleRequest(
          $request->getMethod(),
          "/" . $request->getPathInfo()
      );
  }

  public function controller(): void
  {
    $handlerMethod = $this->route->getHandlerMethod();
    $handlerClass = $this->route->getHandlerClass();
    $parameters = $this->route->getParameters();
    // Instantiate the controller
    $this->controller = $this->container->get($handlerClass);
    // Now we decide what to do
    $controller_response = $this->controller->$handlerMethod(
      ...$parameters
    );
    echo $controller_response;
  }
}
