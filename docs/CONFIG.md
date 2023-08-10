# Nebula PHP Framework - Config Documentation

In the Nebula PHP Framework, configuration files play a vital role in customizing your application's behavior. This guide will walk you through how to use the configuration system in Nebula, including creating and accessing configuration settings.

## Table of Contents

- [Introduction to Config](#introduction-to-config)
- [Config Files Location](#config-files-location)
- [Config Class](#config-class)
  - [Getting Configuration](#getting-configuration)
- [Example Database Config](#example-database-config)
- [Config Helper Function](#config-helper-function)
- [Conclusion](#conclusion)

## Introduction to Config

Configuration settings allow you to define various parameters that control your application's behavior. In the Nebula PHP Framework, configuration files are used to centralize these settings and make them easily accessible throughout your application.

## Config Files Location

All configuration files in Nebula are located in the `/app/Config` directory.

## Config Class

The `Config` class is responsible for loading configuration settings from the configuration files.

### Getting Configuration

To access configuration settings, use the `Config` class as follows:

```php
use App\Config\Config;

$configValue = Config::get('config_name');
```

Where `'config_name'` represents the configuration setting you want to retrieve - the name file name in `/app/Config`. For example, the /app/Config/Database.php array is fetched from config('database');

### Example Database Config

Here's an example of a database configuration file located at `/app/Config/Database.php`:

```php
namespace App\Config;

return [
  "enabled" => env('DB_ENABLED', "true") == "true",
  "mode" => env('DB_MODE'),
  "name" => env('DB_NAME'),
  "host" => env('DB_HOST'),
  "port" => env('DB_PORT'),
  "username" => env('DB_USERNAME'),
  "password" => env('DB_PASSWORD'),
  "charset" => env('DB_CHARSET'),
];
```

You can customize and structure your configuration files as needed, providing clear and organized settings for various components of your application.

## Config Helper Function

A convenient helper function `config()` is provided to access configuration settings:

```php
/**
 * Return the application configuration by name
 */
function config(string $name)
{
  return \App\Config\Config::get($name);
}
```

You can use this helper function to retrieve configuration settings throughout your application. For example:

```php
$databaseConfig = config('database'); // Database config as array
$databaseName = config('database.name'); // 'name' from database config array
$isEnabled = config('database.enabled'); // 'enabled' from database config array
```

## Conclusion

Configuration settings are essential for tailoring your Nebula PHP Framework application to your specific requirements. By utilizing the `Config` class and the helper function, you can conveniently access and modify these settings, ensuring your application behaves as intended.

For advanced usage and to create additional configuration files, refer to the <s>official Nebula documentation</s>.

If you have any questions or need further assistance, don't hesitate to reach out!
