<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Container\Container;
use Nebula\Controllers\Controller;
use StellarRouter\Route;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};

class Web
{
  private ?Route $route;
  private Container $container;
  private Controller $controller;
  private Request $request;
  private Response $response;
  private Router $router;

  /**
   * Run will initialize everything and prepare the response
   */
  public function run(): void
  {
    $this->request = $this->request();
    $this->container = $this->container();
    $this->router = $this->router();
    $this->route = $this->route();
    $this->controller = $this->controller();
    $this->response = $this->response();
    $this->execute();
  }

  /**
   * Instantiate the request
   */
  public function request(): Request
  {
    return Request::createFromGlobals();
  }

  /**
   * Instantiate the router
   */
  public function router(): Router
  {
    // Controller path via getControllers
    $router = $this->container->get(Router::class);
    $config = new \Nebula\Config\Paths();
    $controllers = array_keys(
      ClassMapGenerator::createMap($config->getControllers())
    );
    if ($controllers) {
      foreach ($controllers as $controllerClass) {
        $controller = $this->container->get($controllerClass);
        $router->registerRoutes($controller::class);
      }
    }
    return $router;
  }

  /**
   * Instantiate the DI container
   */
  public function container(): Container
  {
    $container = Container::getInstance();
    $config = new \Nebula\Config\Container();
    $container
      ->setDefinitions($config->getDefinitions())
      ->build();
    return $container;
  }

  /**
   * Instantiate the route
   */
  public function route(): ?Route
  {
    return $this->router->handleRequest(
      $this->request->getMethod(),
      "/" . $this->request->getPathInfo()
    );
  }

  /**
   * Instantiate the controller
   */
  public function controller(): Controller
  {
    $handlerClass = $this->route->getHandlerClass();
    return $this->container->get($handlerClass);
  }

  /**
   * Instantiate the response
   */
  public function response(): JsonResponse|Response
  {
    $handlerMethod = $this->route->getHandlerMethod();
    $routeParameters = $this->route->getParameters();
    $routeMiddleware = $this->route->getMiddleware();
    // Now we decide what to do
    $handlerResponse = $this->controller->$handlerMethod(
      ...$routeParameters
    );
    return in_array('api', $routeMiddleware)
      ? new JsonResponse($handlerResponse)
      : new Response($handlerResponse);
  }

  /**
   * Send the response to the client
   */
  public function execute(): void
  {
    $this->response
      ->prepare($this->request)
      ->send();
  }
}
