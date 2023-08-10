# Nebula PHP Framework - Database Documentation

Welcome to the Database documentation for the Nebula PHP Framework. This guide will help you understand how to configure and interact with the database using Nebula's database classes and functions.

## Table of Contents

- [Introduction to Database Configuration](#introduction-to-database-configuration)
- [Database Configuration](#database-configuration)
- [Database Interface](#database-interface)
  - [Connecting to the Database](#connecting-to-the-database)
  - [Running Queries](#running-queries)
  - [Selecting Data](#selecting-data)
  - [Executing Custom Queries](#executing-custom-queries)
- [Helper Function](#helper-function)
- [Conclusion](#conclusion)

## Introduction to Database Configuration

Database configuration is a crucial aspect of any application. In the Nebula PHP Framework, you can configure your database settings in the `app/Config/Database.php` configuration file.

## Database Configuration

You can configure your database secrets in your `.env` environment file.

Here's an example of a database configuration file:

```php
namespace App\Config;

return [
  "enabled" => true,
  "mode" => env('DB_MODE'),
  "name" => env('DB_NAME'),
  "host" => env('DB_HOST'),
  "port" => env('DB_PORT'),
  "username" => env('DB_USERNAME'),
  "password" => env('DB_PASSWORD'),
  "charset" => env('DB_CHARSET'),
];
```

You can customize the database configuration settings according to your application's requirements.

## Database Interface

The `Database` interface provides methods to connect to the database and run queries.

### Connecting to the Database

To establish a connection to the database, you can use the `connect()` method:

```php
use Nebula\Interfaces\Database\Database;

$dbConfig = config('database'); // Fetch the database configuration
db()->connect($dbConfig); // Connect to the database
```

### Running Queries

The `run()` method is used to run queries on the connected database:

```php
$sql = "INSERT INTO users (name, email) VALUES (?, ?)";
$params = ['John Doe', 'john@example.com'];
db()->run($sql, $params); // Execute the query
```

### Selecting Data

You can use methods like `selectAll()` and `select()` to retrieve data from the database:

```php
$sql = "SELECT * FROM users WHERE age > ?";
$params = [18];
$results = db()->selectAll($sql, ...$params); // Fetch multiple rows

$sql = "SELECT * FROM users WHERE id = ?";
$params = [1];
$user = db()->select($sql, ...$params); // Fetch a single row
```

### Executing Custom Queries

For executing custom queries, you can use the `query()` method:

```php
$sql = "UPDATE users SET name = ? WHERE email > ?";
$params = ['The Primeagen', 'admin@nebula.dev'];
db()->query($sql, ...$params); // Execute the custom query
```

## Helper Function

A convenient helper function `db()` is available to access the database instance:

```php
use Nebula\Interfaces\Database\Database;

function db()
{
  return app()->get(Database::class); // Get the database instance
}
```

You can use this function to easily access the database throughout your application.

## Conclusion

Configuring and interacting with the database in the Nebula PHP Framework is straightforward. By using the provided configuration file, implementing the `Database` interface, and utilizing the helper function, you can effectively manage and interact with your application's database.

For more advanced usage and customization options, please refer to the <s>official Nebula documentation</s>.

If you have any questions or need further assistance, feel free to reach out!
