<?php

namespace Nebula\Http;

use Nebula\Interfaces\Database\Database;
use Dotenv\Dotenv;
use Nebula\Interfaces\Http\{Request, Response};
use Nebula\Interfaces\Routing\Router;
use Nebula\Interfaces\System\Kernel as NebulaKernel;
use Composer\ClassMapGenerator\ClassMapGenerator;
use App\Config\Config;
use Nebula\UI\Twig\Extension;
use Throwable;

class WebKernel implements NebulaKernel
{
    private Router $router;
    private Database $db;
    private Dotenv $dotenv;

    /**
     * Setup the application
     */
    public function setup(): WebKernel
    {
        $this->registerInterfaces();
        $this->initRouter();
        return $this;
    }

    /**
     * Register all default the framework interface binding
     */
    protected function registerInterfaces(): void
    {
        app()->singleton(\Nebula\Interfaces\Database\Database::class, \Nebula\Database\MySQLDatabase::class);
        app()->bind(\Nebula\Interfaces\Routing\Router::class, \Nebula\Routing\Router::class);
        app()->bind(\Nebula\Interfaces\Http\Response::class, \Nebula\Http\Response::class);
        app()->bind(\Twig\Environment::class, function() {
            $config = app()->get(Config::class)::twig();
            $loader = new \Twig\Loader\FilesystemLoader($config["view_path"]);
            $twig = new \Twig\Environment($loader, [
                "cache" => $config["cache_path"],
                "auto_reload" => true,
            ]);
            $twig->addExtension(new Extension);
            return $twig;
        });
    }

    private function initRouter(): void
    {
        $this->router = app()->get(Router::class);
        // Register the controller classes
        $config = app()->get(Config::class)::paths();
        foreach ($this->classMap($config['controllers']) as $class_name => $filename) {
            $this->router->registerClass($class_name);
        }
    }

    public function getEnvironment(string $name): ?string
    {
        if (!isset($this->dotenv)) {
            // Load environment variables
            $config = app()->get(Config::class)::paths();
            $this->dotenv = Dotenv::createImmutable($config['app_root']);
            $this->dotenv->safeLoad();
        }
        return isset($_ENV[$name])
            ? $_ENV[$name]
            : null;
    }

    public function getDatabase(): Database
    {
        if (!isset($this->db)) {
            $this->db = app()->get(Database::class);
            $config = app()->get(Config::class)::database();
            $this->db->connect($config);
        }
        return $this->db;
    }

    /**
     * @return array<class-string,non-empty-string>
     */
    public function classMap(string $directory): array
    {
        if (!file_exists($directory)) {
            throw new \Exception("class map directory doesn't exist");
        }
        return ClassMapGenerator::createMap($directory);
    }

    /**
     * Handle the request and return a response
     */
    public function handleRequest(Request $request): Response
    {
        $response = app()->get(Response::class);
        $route = $this->router->handleRequest($request->getMethod(), $request->getUri());

        if ($route) {
            try {
                $handlerClass = $route->getHandlerClass();
                $handlerMethod = $route->getHandlerMethod();
                $routeParameters = $route->getParameters();
                $class = new $handlerClass();

                $content = $class->$handlerMethod(...$routeParameters);
                $response->setContent($content ?? '');

            } catch (\Exception $ex) {
                $this->handleException($ex);
            }
        } else {
            // TODO 404 page
            $response->setStatusCode(404);
            $response->setContent('Page not found.');
        }

        return $response;
    }

    /**
     * Handle any application exceptions
     */
    public function handleException(Throwable $exception): void
    {
        // TODO deal with the exception
        $response = app()->get(Response::class);
        $response->setStatusCode(404);
        $response->setContent('Server error.');
        $response->send();
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    {
        exit;
    }
}

