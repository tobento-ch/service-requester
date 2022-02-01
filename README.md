# Requester Service

A PSR-7 server request wrapper class providing simplified methods.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
- [Documentation](#documentation)
    - [Create Requester](#create-requester)
    - [Available Methods](#available-methods)
- [Credits](#credits)
___

# Getting started

Add the latest version of the requester service project running this command.

```
composer require tobento/service-requester
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Create Requester

```php
use Tobento\Service\Requester\Requester;
use Tobento\Service\Requester\RequesterInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

// Any PSR-7 server request
$serverRequest = (new Psr17Factory())->createServerRequest(
    method: 'GET',
    uri: 'https://example.com',
);

$requester = new Requester($serverRequest);

var_dump($requester instanceof RequesterInterface);
// bool(true)
```

## Available Methods

**method**

Returns the HTTP method in uppercase such as GET, POST, PUT...

```php
var_dump($requester->method());
// string(3) "GET"
```

**isSecure**

Returns whether the request is secure or not.

```php
var_dump($requester->isSecure());
// bool(false)
```

**isContentType**

Determine if the request is of the specified content type.

```php
var_dump($requester->isContentType('application/json'));
// bool(false)
```

**isAjax**

Check if request was via AJAX.

```php
var_dump($requester->isAjax());
// bool(false)
```

**isJson**

Determine if the request is sending JSON.

```php
var_dump($requester->isJson());
// bool(false)
```

**json**

Returns the request JSON payload.\
Check out the [Collection Service](https://github.com/tobento-ch/service-collection#collection) to learn more about the Collection in general.

```php
use Tobento\Service\Collection\Collection;

var_dump($requester->json() instanceof Collection);
// bool(true)
```

**input**

Returns the request input data. Depending on the content type and method, it returns the parsed body data or the query params. \
Check out the [Collection Service](https://github.com/tobento-ch/service-collection#collection) to learn more about the Collection in general.

```php
use Tobento\Service\Collection\Collection;

var_dump($requester->input() instanceof Collection);
// bool(true)
```

**uri**

Returns the Uri.

```php
use Psr\Http\Message\UriInterface;

var_dump($requester->uri() instanceof UriInterface);
// bool(true)
```

**request**

Returns the server request.

```php
use Psr\Http\Message\ServerRequestInterface;

var_dump($requester->request() instanceof ServerRequestInterface);
// bool(true)
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)