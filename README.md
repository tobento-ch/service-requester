# Error Handler Service

The Error Handler Service provides tools to manage errors and exceptions.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
- [Documentation](#documentation)
    - [Error Handling](#error-handling)
    - [Throwable Handlers](#throwable-handlers)
        - [Create Throwable Handlers](#create-throwable-handlers)
        - [Add Throwable Handler](#add-throwable-handler)
        - [Restrict Throwable Handler](#restrict-throwable-handler)
        - [Prioritize Throwable Handler](#prioritize-throwable-handler)
        - [Handle Throwable](#handle-throwable)
        - [Handlers](#handlers)
            - [Debug](#debug)
            - [Errors](#errors)
            - [Log](#log)
- [Credits](#credits)
___

# Getting started

Add the latest version of the error handler service project running this command.

```
composer require tobento/service-error-handler
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Error Handling

Check out [Throwable Handlers](#throwable-handlers) to learn more about the Throwable Handlers.

```php
use Tobento\Service\ErrorHandler\ErrorHandling;
use Tobento\Service\ErrorHandler\ThrowableHandlers;
use Tobento\Service\ErrorHandler\ThrowableHandlerFactory;
use Tobento\Service\ErrorHandler\Handler;

$throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());

// adding any handler:
// $throwableHandlers->add(ValidationExceptionHandler::class);
// $throwableHandlers->add(GeneralExceptionHandler::class);

// only on development:
$throwableHandlers->add(Handler\Debug::class);

// adding last:
$throwableHandlers->add(Handler\Errors::class);

(new ErrorHandling($throwableHandlers))->register();
```

## Throwable Handlers

The throwable handlers can be used where you need to handle exceptions in general.

### Create Throwable Handlers

```php
use Tobento\Service\ErrorHandler\ErrorHandling;
use Tobento\Service\ErrorHandler\ThrowableHandlers;
use Tobento\Service\ErrorHandler\ThrowableHandlersInterface;
use Tobento\Service\ErrorHandler\ThrowableHandlerFactory;

$throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());

var_dump($throwableHandlers instanceof ThrowableHandlersInterface);
// bool(true)
```

**With autowiring factory**

The autowiring factory is needed if you [Add Throwable Handlers](#add-throwable-handler) with dependencies.

```php
use Tobento\Service\ErrorHandler\ErrorHandling;
use Tobento\Service\ErrorHandler\ThrowableHandlers;
use Tobento\Service\ErrorHandler\ThrowableHandlersInterface;
use Tobento\Service\ErrorHandler\AutowiringThrowableHandlerFactory;

// Any PSR-11 container
$container = new \Tobento\Service\Container\Container();

$throwableHandlers = new ThrowableHandlers(
    new AutowiringThrowableHandlerFactory($container)
);

var_dump($throwableHandlers instanceof ThrowableHandlersInterface);
// bool(true)
```

### Add Throwable Handler

**By class instance**

```php
$throwableHandlers->add(new Handler\Errors());
```

**By class name**

```php
$throwableHandlers->add(Handler\Errors::class);
```

**By class name with build-in parameters (not resolvable by autowiring)**

```php
$throwableHandlers->add([ThrowableHandler::class, 'name' => 'value']);
```

**By anonymous function**

```php
use Throwable;

$throwableHandlers->add(function(Throwable $t): null|Throwable {
    // Return null if can handle throwable, otherwise throwable.
    return $t;
});
```

### Restrict Throwable Handler

**By error level(s)**

The throwable handler will only be used on the specified error levels.

```php
$throwableHandlers->add(ThrowableHandler::class)
                  ->level(\E_USER_WARNING, \E_WARNING);
```

**By exception(s)**

The throwable handler will only be used on the specified exceptions.

```php
$throwableHandlers->add(ThrowableHandler::class)
                  ->handles(SomeException::class, AnotherException::class);
```

### Prioritize Throwable Handler

You might want to prioritize the excution order of the handlers by the following way (highest first):

```php
$throwableHandlers->add(ThrowableHandler::class)
                  ->priority(1000); // is default
```

### Handle Throwable

```php
use Throwable;

try {
    // do something
} catch (Throwable $t) {
    // do something with the response:
    $response = $throwableHandlers->handleThrowable($t);
}
```

### Handlers

#### Debug

The debug handler will render a debugging page on any exception not caught.

```php
use Tobento\Service\ErrorHandler\Handler\Debug;
use Tobento\Service\View\ViewInterface;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;

$debug = new Debug(
    view: null, // null|ViewInterface
);

var_dump($debug instanceof ThrowableHandlerInterface);
// bool(true)
```

**Custom view**

Check out [View Service](https://github.com/tobento-ch/service-view) to learn more about the View in general.

```
private/
    view/
        debug/
            error.php
            throwable.php
```

```php
use Tobento\Service\ErrorHandler\Handler\Debug;
use Tobento\Service\View\ViewInterface;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;

$view = new View(
    new PhpRenderer(
        new Dirs(
            new Dir('/private/view'),
        )
    )
);

$debug = new Debug(
    view: $view, // null|ViewInterface
);
```

#### Errors

The errors handler will render an error page on shutdown.

```php
use Tobento\Service\ErrorHandler\Handler\Errors;
use Tobento\Service\View\ViewInterface;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;

$errors = new Errors(
    view: null, // null|ViewInterface
);

var_dump($errors instanceof ThrowableHandlerInterface);
// bool(true)
```

**Custom error view**

Check out [View Service](https://github.com/tobento-ch/service-view) to learn more about the View in general.

```
private/
    view/
        error.php
```

```php
use Tobento\Service\ErrorHandler\Handler\Errors;
use Tobento\Service\View\ViewInterface;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;

$view = new View(
    new PhpRenderer(
        new Dirs(
            new Dir('/private/view'),
        )
    )
);

$errors = new Errors(
    view: $view, // null|ViewInterface
);
```

#### Log

The log handler will log any errors or exceptions.

```php
use Tobento\Service\ErrorHandler\Handler\Log;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;

$logger = new Logger('name');
$logger->pushHandler(new TestHandler());

$log = new Log(
    logger: $logger, // Closure|LoggerInterface
);

var_dump($log instanceof ThrowableHandlerInterface);
// bool(true)
```

**Example with Closure and throwable handlers**

```php
use Tobento\Service\ErrorHandler\Handler\Log;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

$throwableHandlers->add(new Log(function(): LoggerInterface {
    $logger = new Logger('name');
    $testHandler = new TestHandler();
    $logger->pushHandler($testHandler);
    return $logger;
}))->levels(E_ERROR, E_CORE_ERROR);
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)