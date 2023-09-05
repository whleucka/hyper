# Nebula PHP Framework - Views Documentation

Welcome to the views documentation for the Nebula PHP Framework. This guide will introduce you to the concept of views, how to render them using the Latte/Twig template engine, and provide examples of rendering both HTML templates and plain text responses.

## Table of Contents

- [Introduction to Views](#introduction-to-views)
- [Rendering Latte Views](#rendering-latte-views)
  - [Using the `latte` Helper Function](#using-the-latte-helper-function)
  - [Example: Rendering a Latte View in a Controller](#example-rendering-a-latte-view-in-a-controller)
- [Rendering Twig Views](#rendering-twig-views)
  - [Using the `twig` Helper Function](#using-the-twig-helper-function)
  - [Example: Rendering a Twig View in a Controller](#example-rendering-a-twig-view-in-a-controller)
- [Rendering Plain Text Responses](#rendering-plain-text-responses)
  - [Example: Rendering Plain Text in a Route](#example-rendering-plain-text-in-a-route)
- [Conclusion](#conclusion)

## Introduction to Views

Views are a crucial part of web development as they allow you to display dynamic content to users. In the Nebula PHP Framework, you have the flexibility to render both HTML templates using the Latte/Twig template engine and plain text responses.

## Template Path

View templates are stored in the `/views` directory.


## Rendering Latte Views

### Using the `latte` Helper Function

Nebula also provides a convenient `latte` helper function to render Latte templates.

```php
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
```

### Example: Rendering a Latte View in a Controller

You can also use the `latte` helper function in your controllers to render Latte views. 

```php
use Nebula\Controller\Controller;
use StellarRouter\Get;

#[Get("/sign-in", "sign-in.index")]
public function index(): string
{
  return latte("admin/auth/sign-in.latte");
}
```


## Rendering Twig Views

### Using the `twig` Helper Function

Nebula provides a convenient `twig` helper function to render Twig templates. This function simplifies the process of rendering and passing data to your views.

```php
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
```

### Example: Rendering a Twig View in a Controller

You can use the `twig` helper function in your controllers to render Twig views. Here's an example of how to render a Twig view in a controller action:

```php
use Nebula\Controller\Controller;
use StellarRouter\Get;

#[Get("/sign-in", "sign-in.index")]
public function index(): string
{
  return twig("admin/auth/sign-in.html", []);
}
```


## Rendering Plain Text Responses

You can also render plain text responses using the Nebula framework. This is useful when you need to return simple textual content to the user.

### Example: Rendering Plain Text in a Route

Here's an example of how to render plain text content in a route using the Nebula framework:

```php
$app->route('GET', '/', function() {
    return "Hello, world!";
}, middleware: ['cached']);
```

## Conclusion

The Nebula PHP Framework provides a versatile approach to rendering views. You can easily render dynamic HTML templates using the `twig` helper function and the Twig template engine. Additionally, you can render plain text responses for simpler content. This documentation equips you with the knowledge to effectively render views and provide dynamic content to users.

For more advanced view techniques, customization options, and integrations, please consult the <s>official Nebula documentation</s>.

Feel free to reach out if you have any questions or need further assistance!
