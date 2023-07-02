# Nebula
[![PHP Composer](https://github.com/libra-php/nebula/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/libra-php/nebula/actions/workflows/php.yml)

⭐ Nebula is a powerful PHP web framework

✅ Provides developers with a flexible and extensible architecture to build custom web applications with ease.

👷 *Currently under development*

❌ **Not ready for production use**


### Features for v0.0.1
- [x] Database Abstraction: Simplified database interactions with <a href='https://github.com/libra-php/galaxy-pdo'>GalaxyPDO</a>.
- [x] Error Handling: Comprehensive error handling and logging for efficient debugging.
- [x] Form Handling: Convenient form handling and validation capabilities.
- [x] Routing: <a href='https://github.com/libra-php/stellar-router'>StellarRouter</a> for handling URL mapping and request handling.
- [x] Templating: Built-in template engine support for easy and flexible view rendering.
- [x] Caching: Caching mechanisms to improve performance and optimize data retrieval.
- [ ] Security: Integrated security measures to protect against common web vulnerabilities.
- [ ] Admin Access Layer: wip
- [ ] Migrations: wip
- [ ] Logging: wip
- [ ] Docker Environment: wip


### TODO
- Two-factor authentication
- Look up route by name & build route from args
- Support for other template engines
- Docker development environment
- Investigate PHP logging solutions
- Writing tests that cover various components of Nebula
- Refactor and benchmark


### Getting Started

To get started with Nebula, follow these steps:

- **Installation**: Clone the repository and install dependencies using Composer.
```bash
git clone https://github.com/libra-php/nebula.git
cd nebula
composer install
```

- **Configuration**: Customize the configuration files according to your project requirements, including database settings and routes.
```
# Copy the example settings
cp .env.example .env
# Change ownership of the view cache
chown -R www-data:www-data views/.cache
```

- **Development**: Start building your application by creating controllers, views, and models within the `src` directory. You can start a local development server by running `./bin/serve`

- **Testing**: Nebula includes a testing suite powered by PHPUnit. Add your tests under the tests directory and run them with: `./bin/test` or `composer run-script test` or `./vendor/bin/phpunit tests`. If you have `siege` installed, then you can benchmark the framework by running `./bin/benchmark`. Make sure you edit the script and change the host name to match your environment.

- **Deployment**: Once your application is ready for deployment, configure your web server to point to the public directory as the document root.


### Routing

Routing is super easy with Nebula. Call a class method or simply define a closure that returns a payload.

- With app's http method helpers, you can easily wire up the routing for your app. Here is a basic example of `/public/index.php` which creates 3 routes.
```php
<?php
require_once "bootstrap.php";

class TestController extends Nebula\Controllers\Controller
{
  public function test(): int { return 42; }
}

app()
  ->get("/", payload: fn() => "hello, world!")
  ->get("/view", payload: fn() => twig("home/index.html", ["msg" => "hello, world!"]))
  ->post("/test", "TestController", "test", middleware: ["api"])
  ->run();
```


###  Attribute-based Routing

We also support attribute routing. You can specify the route above the target endpoint in the desired controller. All RESTful HTTP methods are supported. Awesome!

- Here is an example controller located at `/src/Controllers/HomeController.php`
```php
<?php
namespace Nebula\Controllers;

use StellarRouter\{Get, Post};

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): string
    {
        // This is a web response
        return "hello, world!";
    }

    #[Get("/view", "home.view")]
    public function view(): string
    {
        // This is a twig web response
        // msg is a variable that is accessible in the template
        return twig("home/index.html", ["msg" => "hello, world!"]);
    }

    #[Post("/test", "home.test", ["api"])]
    public function test(): int
    {
        // This is a JSON response (from api route middleware)
        return 42;
    }
}
```

<s>For more detailed instructions and documentation, please refer to the <a href='#'>Nebula Documentation</a></s>


### Benchmarks

Here are a few sample test results using the `siege` tool. Our team is dedicated to optimizing Nebula to deliver exceptional performance, striving to position it among the top-performing PHP frameworks available 🚀 

Nebula dev:
```
Lifting the server siege...
Transactions:		      13346 hits
Availability:		     100.00 %
Elapsed time:		       1.92 secs
Data transferred:	       0.17 MB
Response time:		       0.00 secs
Transaction rate:	    6951.04 trans/sec
Throughput:		       0.09 MB/sec
Concurrency:		       9.77
Successful transactions:       13346
Failed transactions:	          0
Longest transaction:	       0.01
Shortest transaction:	       0.00
```

Based on this results, Nebula achieved a transaction rate of 6,951.04 trans/sec. Leaf achieved 7,204.38 trans/sec, and Laravel achieved 632.77 trans/sec.

These numbers demonstrate the strong performance of Nebula and Leaf, making them excellent choices for high-traffic applications. 

While considering these numbers, it's important to approach them with caution. Benchmarking frameworks on my personal network and hardware may not yield an entirely accurate measure of requests per second (RPS). Nonetheless, based on my own tests, this type of benchmarking does reveal a noticeable latency.

- *Command: `siege -b -c 10 -t 1s $APP_URL`*
- *Server is on LAN*


### Contributing

Contributions to Nebula are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request. 


### License

This project is licensed under the <a href='https://github.com/libra-php/nebula/blob/main/LICENSE'>MIT License</a>.


### Acknowledgements

We would like to express our gratitude to the following open-source projects that have inspired Nebula:

- Symfony
- Slim Framework
- Leaf
- Laravel


### Contact

For any inquiries or questions, please contact william.hleucka@gmail.com.


🇨🇦 Made in Canada
