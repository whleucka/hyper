# Nebula

⭐ Nebula is a powerful PHP web framework inspired by the vastness and beauty of the cosmos. It provides developers with a flexible and extensible architecture to build custom web applications with ease. Also, did we mention it was blazingly fast? 🚀

👷 *Currently under development*

❌ **Not ready for production use**

### Features
- [x] Modular Design: Nebula follows a modular approach, allowing you to organize your code into reusable and independent components.
- [x] Routing: Efficient routing system for handling URL mapping and request handling.
- [x] Template Engine: Built-in template engine for easy and flexible view rendering.
- [ ] Database Abstraction: Simplified database interactions with a built-in ORM (Object-Relational Mapping) layer.
- [ ] Form Handling: Convenient form handling and validation capabilities.
- [x] Security: Integrated security measures to protect against common web vulnerabilities.
- [x] Caching: Caching mechanisms to improve performance and optimize data retrieval.
- [x] Error Handling: Comprehensive error handling and logging for efficient debugging.


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

- **Development**: Start building your application by creating controllers, views, and models within the src directory. You can start a local development server by running `./bin/serve`

- **Testing**: Nebula includes a testing suite powered by PHPUnit. Add your tests under the tests directory and run them with: `./bin/test` or `composer run-script test` or `./vendor/bin/phpunit tests`. If you have `siege` installed, then you can benchmark the framework by running `./bin/benchmark`. Make sure you edit the script and change the hostname to match your environment.

- **Deployment**: Once your application is ready for deployment, configure your web server to point to the public directory as the document root.

### Basic usage

Here is an extremely simple example of how you can build a route with a simple controller. Endpoints can be either class methods or closures! In this example, we define a GET and POST route.

file: public/index.php
```php
<?php
require_once "bootstrap.php";

class TestController extends Nebula\Controllers\Controller
{
  public function test(): int { return 42; }
}

app()
  ->get("/", payload: fn() => "hello, world!")
  ->post("/test", "TestController", "test", middleware: ["api"])
  ->run();
```

###  A better use case

We also support attribute routing, which is the preferred way of routing in Nebula. You can specify the route above the target endpoint in the desired controller. All RESTful HTTP methods are supported. You can even define the route name and attach route middleware. How easy is that!?


file: src/Controllers/HomeController.php
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

    #[Post("/test", "home.test", ["api"])]
    public function test(): int
    {
        // This is a JSON response (from api middleware)
        return 42;
    }
}
```

That's it! 

<s>For more detailed instructions and documentation, please refer to the <a href='#'>Nebula Documentation</a></s>


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

Let the Nebula framework guide you through the cosmos of web development, and create stellar applications that shine bright in the digital universe.


🇨🇦 Made in Canada
