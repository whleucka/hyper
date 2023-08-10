# Nebula PHP Framework - Helpers Documentation

Welcome to the Helpers documentation for the Nebula PHP Framework. This guide will introduce you to various utility functions that are designed to assist you in common tasks within your application. These functions are available globally and can be used across different parts of your codebase. We'll cover a range of helper functions that provide functionalities such as debugging, logging, redirection, environment access, and more.

## Table of Contents

- [Introduction to Helpers](#introduction-to-helpers)
- [Available Helper Functions](#available-helper-functions)
  - [Debugging](#debugging)
  - [Redirection](#redirection)
  - [Logging](#logging)
  - [IP Address](#ip-address)
  - [Application Singleton](#application-singleton)
  - [Request Singleton](#request-singleton)
  - [Configuration Access](#configuration-access)
  - [Environment Access](#environment-access)
  - [Database Access](#database-access)
  - [Session Access](#session-access)
  - [Twig Rendering](#twig-rendering)
- [Conclusion](#conclusion)

## Introduction to Helpers

Helpers are globally accessible utility functions provided by the Nebula PHP Framework. These functions are intended to streamline common tasks, improve development efficiency, and enhance code readability.

## Helpers Path

The helper functions are located at `/src/Helpers/functions.php`. This file is globally accessible and is already included.

## Available Helper Functions

### Debugging

- `dump(...$args)`: Dump and display arguments.
- `dd(...$args)`: Dump arguments and terminate the script.

### Redirection

- `redirect(string $url, int $code = 301, int $delay = 0)`: Redirect to the specified URL.
- `redirectRoute(string $name, int $code = 301, int $delay = 0)`: Redirect to a named route.

### Logging

- `initLogger()`: Initialize the application logger.
- `logger(string $level, string $message, string $title = '')`: Log messages with different severity levels.

### IP Address

- `ip()`: Get the client's IP address.

### Application Singleton

- `app()`: Get the application singleton instance.

### Request Singleton

- `request()`: Get the application request singleton instance.

### Configuration Access

- `config(string $name)`: Access application configuration by name. You may access config("database") for configuration array, or config("database.enabled") for enabled setting of database configuration array.

### Environment Access

- `env(string $name, ?string $default = null)`: Access environment variables.

### Database Access

- `db()`: Get the application database instance.

### Session Access

- `session()`: Get the application session instance.

### Twig Rendering

- `twig(string $path, array $data = []): string`: Render a Twig template.

## Conclusion

The Helpers provided by the Nebula PHP Framework offer a wide range of functionalities to simplify and enhance your application development experience. These functions are designed to cover various common scenarios, from debugging and logging to accessing configuration and database instances. By incorporating these helper functions into your codebase, you can streamline your development workflow and write cleaner, more efficient code.

For more information on advanced usage and customization of these helper functions, refer to the <s>official Nebula documentation</s>.

If you have any questions or need further assistance, feel free to reach out!
