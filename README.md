# adagio/middleware

[![Latest Stable Version](https://poser.pugx.org/adagio/middleware/v/stable)](https://packagist.org/packages/adagio/middleware)
[![Build Status](https://travis-ci.org/adagiolabs/middleware.svg)](https://travis-ci.org/adagiolabs/middleware)
[![License](https://poser.pugx.org/adagio/middleware/license)](https://packagist.org/packages/adagio/middleware)
[![Total Downloads](https://poser.pugx.org/adagio/middleware/downloads)](https://packagist.org/packages/adagio/middleware)

[`adagio/middleware`](https://github.com/adagiolabs/middleware) library allows to
implement middlewares with various data types easily.


## Installation

Install [Composer](https://getcomposer.org) and run the following command to get
the latest version:

```bash
composer require adagio/middleware
```

## Quick start

todo.


## Middlewares principles

  - A middleware signature MUST include the input data, the output data to hydrate
    and the next middleware to call.
  - A middleware MUST return a valid output data.
  - A middle ware CAN process data before or after calling the next midleware.
  - The very last middleware "hidden in the stack" just returns the output data.


## Transition to middlewares

Imagine you want to add a middleware pipeline to an existing image-editing library.

Here is the way you can do it without middlewares:

```php
// I want to solarize, rotate, unblur and then sepia my image (parameters are 
// voluntarily omitted for clarity).
$image = (new SepiaFilter)
    ->filter((new UnblurFilter)
    ->filter((new RotateFilter)
    ->filter((new SolarizedFilter)
    ->filter(new Image('/path/to/image')))));
```

Problems are:

  - you have to declare the pipeline backward
  - big parenthesis mess
  - you cannot declare a pipeline to use on other images later

With [`adagio/middleware`](https://github.com/adagiolabs/middleware), you can do
it easily:

```php
use Adagio\Middleware\Stack;

$pipe = new Stack([
    new SolarizedFilter,
    new RotateFilter,
    new UnblurFilter,
    new SepiaFilter,
]);

$image = $stack(new Image('/path/to/image'));
```

Filters have just to respect the following signature convention:

```php
function (Image $image, callable $next): Image
{
    // Maybe do something with $image
    
    $resultingImage = $next($image);
    
    // Maybe do something with $resultingImage
    
    return $resultingImage;
}
```

Each filter must pass the `$image` to the `$next` element of the pipe and can modify it before of after passing it.

Filters can be any callable respecting the given signature.


## More complex example: DB query processing

Middlewares are even more useful when the given and the returned objects are different.
Think about a SQL query processor with the following signature:

```php
function (SqlQuery $query, ResultSet $resultSet, callable $next): ResultSet
```

You can then provide a caching middleware:

```php
final class QueryCache
{
    // ...

    public function __invoke(SqlQuery $query, ResultSet $resultSet, callable $next): ResultSet
    {
        // If the query is already in cache, return the ResultSet and don't 
        // trigger the rest of the middleware stack
        if ($this->resultSetCache->hasQuery($query)) {
            return $this->resultSetCache->getFromQuery($query);
        }

        $finalResultSet = $next($query, $resultSet);

        $this->resultSetCache->add($query, $finalResultSet);

        return $finalResultSet;
    }
}
```

You can also provide a middleware that translates from a SQL standard to another,
a SQL validator, a client-side cluster/shard solution, a logger, a performance monitor, ...
