# Figment Container

Figment Container contains the fundamental DI concept of the Figment framework.

For simplicities’ sake, all objects that can be created by the container are called **service**, even if that might not be fully correct in the context of your chosen paradigm - service, repository, helper, factory... instances of all these classes are simply called service.

## Fundamental design philosophy

### Keep it PHP

The framework tries to keep as much as possible within PHP and does not use other types of files to do its job. We will never use YAML, JSON, XML or other types of configuration files, as long as the same job can be achieved by using PHP files.

The developer is assumed to know PHP - why else would they use this framework? So why add a configuration in an annoying, not opt-cached file format, when we can simply use PHP and have the entire power of the language and our IDE to guide us?

### Lazy is fast

The container is built on the principle of lazy instantiation of objects. The framework makes heavy use of [lazy objects](https://www.php.net/manual/de/language.oop5.lazy-objects.php) to fully load services as late as possible.

### Control is everything

The system leaves a lot of control to the developer using the framework. Dependencies can be realigned, singleton can be controlled as needed and many things can be overwritten at any level.

Even the way objects are created is controlled during configuration of the container, allowing the developer to completely bypass the systems behaviour of injecting via constructor parameters. Own factories and generators for services can be registered and used with ease. This also allows the container to interact with other frameworks with ease.

### Pulling is better than pushing

When writing a new service in development it is better to work in a single file.

To make this easier the framework tries to put as much DI information as possible in the classes themselves, using attributes to define what information is requested.

All you need to do is: Write your class and then register it with the configuration.

This also makes it easier to later add new dependencies to existing classes: Simply add a new parameter to the constructor, mark it with the corresponding attribute and the framework will inject the desired service or context into the parameter (provided that dependency is already registered in the configuration by someone else).

# How does it work?

## Context

Any injected values are called "context". A few examples of context data might be:
- Database connection data
- File system paths
- HTTP endpoint paths
- Logging parameters

On framework level the context is defined as a key-value map, where the key is a string (the name of the context field) and the value can be anything.

> [!TIP]
> It is best practice to group and separate context field names with `.`. A good example would be a field `database.password`
> 
> Please be aware that the context does not contain any grouping magic by default. If only the `database.password` and a field `database.user` are defined, the framework will not automagically provide a field `database` containing both information.
> 
> This behaviour however is highly dependent on the used context implementation.

There is no magic in defining the context. You can set a global context in the configuration and overwrite/extend it as needed on a per-service basis.

A few default implementations of the `\Philiagus\Figment\Container\Contract\Context` interface can be found in the `\Philiagus\Figment\Container\Context` namespace. It is usally best to set the context in a dedicated `config.php`.

### Existing Implementations
#### ArrayLookupContext

The most commonly used context is the `\Philiagus\Figment\Container\Context\ArrayLookupContext`.

The class expects an array and then uses the separated parts of the path to recursively search in the array for the desired configuration element. By default, the path is split by `.`, but this can be defined via a constructor parameter.

```php
use Philiagus\Figment\Container\Context\ArrayLookupContext;

$context = new ArrayLookupContext([
    'database' => [
        'username' => '<your user>',
        'password' => '<your password>'
    ]
]);

$context->get('database.username'); // <your user>
$context->get('database.password'); // <your password>
$context->get('database'); // ['username' => '<your user>', 'password' => '<your password>']
```

#### SimpleContext

The `\Philiagus\Figment\Container\Context\SimpleContext` is mapping the requested context name to the key of an array without doing any deeper search.

```php
use Philiagus\Figment\Container\Context\SimpleContext(
    [
        'database.username' => '<your user>',
        'database.password' => '<your password>'
    ]
);

$context->get('database.username'); // <your user>
$context->get('database.password'); // <your password>
$context->get('database'); // throws UndefinedContextException
```

### GateContext

A proxy-context that allows access only to the defined list of allowed context names and/or a regular expression defining the allowed context names.

### MappingContext

A proxy-context that allows mapping names to other names before requesting those mapped names from the proxied context.

It also allows to
- fallback to non-mapped request if the mapped value isn't found and to
- request the name directly if no map is defined for it

Both are _off_ by default.

#### FallbackContext

A proxy-context that iterates through its provided context children in order to return the first found value. This class is mainly used by the framework to allow for context overwrites.

#### EmptyContext

That should be self-explanatory, I do believe.

### Using Environment Variables

Using environment variables is simply a stack of existing context classes:

```php
use Philiagus\Figment\Container\Context;

// Option 1
$envContext = new Context\MappingContext(
    new Context\FallbackContext(
        new Context\SimpleContext($_ENV),
        new Context\SimpleContext($_SERVER)
    ),
    [
        'database.username' => 'YOUR_ENV_DB_USERNAME',
        'database.password' => 'YOUR_ENV_DB_PASSWORD',
    ]
);

// Option 2
$envContext = new Context\ArrayLookupContext(
    'database' => [
        'username' => $_SERVER['YOUR_ENV_DB_USERNAME'],
        'password' => $_SERVER['YOUR_ENV_DB_PASSWORD']
    ]
);
```

### Configuration

The configuration is the main entrypoint of the framework. The first thing you have to do is create a new instance of the `Configuration` class and use it to configure your services.

```php
use Philiagus\Figment\Container\Configuration;

$config = new Configuration(
    // you can provide your "global context" as parameter here
);
```

## Project setup example

This example splits its code in multiple files, which is the recommended way of doing things.

Your project will (in most cases) have the following structure:

Files marked `[I]` should be excluded from your revision tracking (.gitignore).
```
{project root}/
 ├ app/         // directory containing the bootsrapping files
 │  ├ .gitignore        // .gitignore ignoring the context.php file
 │  │
 │  ├ context.php [I]   // Individually created per environment, but on
 │  │                   // production should be identical to context.php.dist
 │  │
 │  ├ context.php.dist  // dist file that is the basis of the context.php
 │  │
 │  ├ container.php     // file configuring and returning a container instance
 │  ├ container.*.php   // further files the container.php deligates content to
 │  └ container.*.php   // ...
 │
 ├ vendor/ [I]  // directory created and managed by composer
 │  ┊
 │  └ autoload.php      // autoloader created by composer
 │
 ├ src/         // directory containing your source code as defined by PSR-4
 │
 ├ test/        // directory containing your automated tests as defined by PSR-4
 │
 ├ web/         // if your project acts as an HTTP endpoint it is best to
 │  │           // bundle all publically accessable files into a sub-directory
 │  │
 │  └ entry.php         // most PHP projects use a single entry file that server
 │                      // routes to, which should be located in here
 ┊
 ┊ // further files and directories as needed
 ┊
 ├ .gitignore           // your projects .gitignore file
 │
 ├ bootstrap.php        // The bootstrap file is the first thing you request
 │                      // as it builds up your environment and provides the
 │                      // container in the context of your project
 │
 ├ composer.json        // your composer.json configuration
 ├ composer.lock        // automatically generated file by composer
 │
 └ figment              // An executeable file containing PHP
```


### {project root}/bootstrap.php

The file 

```php
<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return require __DIR__ . '/app/container.php';
```

### {project root}/app/context.php

Configuration file, usually built from a `context.php.dist`, but can be individually overwritten for local development if needed.

```php
<?php
declare(strict_types=1);

use Philiagus\Figment\Container\Context;

return new Context\MappingContext(
    new Contract\FallbackContext(
        new Context\SimpleContext($_ENV),
        new Context\SimpleContext($_SERVER)
    ),
    [
        'database.username' => 'MY_ENV_USERNAME',
        'database.password' => 'MY_ENV_PASSWORD'
    ]
);
```

### {project root}/app/container.php
Configuration file, defining the services by configuring the container. This example will also show how splitting up services is recommended
```php
<?php
declare(strict_types=1);

use Philiagus\Figment\Container;

/** @var Container\Contract\Context $context */
$context = require __DIR__ . '/context.php';

$config = new Container\Configuration($context);

// example for how to split up the configuration in multiple files
// Please choose the level of split that makes sense for you (if any)
//
// By the magic of PHP those files will contain the $context and $config
// variables, so you can use them as needed
require __DIR__ . '/container.a-certain-split.php';
require __DIR__ . '/container.a-separate-split.php';

return $config->getContainer();
```

### {project root}/web/entry.php

```php
<?php
declare(strict_types=1);

use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Http\DTO\Request;

try {

    /** @var Container $container */
    $container = require __DIR__ . '/../bootstrap.php';
    $request = Request::fromGlobals();
    $httpWorker = $container->get('figment.http.worker');
    $httpWorker->work($request);
    
} catch (\Throwable $e) {
    // Global "if everything else fails" error handling, most important
    // if your setup fails entirely
    http_send_status(500);
    
    // remove nullbytes as error_log is not binary safe
    // you can only pray that this message will only contain utf-8 characters!
    $logMessage = str_replace("\0", "\\0", (string)$e);
    error_log($logMessage);
}

```

### {project root}/figment

```php
#!/usr/bin/env php
<?
declare(strict_types=1);

use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\CLI\Terminal;

/** @var Container $container */
$container = require __DIR__ . '/bootstrap.php';
$terminal = Terminal::default();
$container->get('figment.cli.worker')->work($terminal);
```
