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

**isReading**

Determine if the HTTP request is a reading request which is the case if the method is one of HEAD, GET and OPTIONS.

```php
var_dump($requester->isReading());
// bool(true)
```

**isPrefetch**

Determine if the HTTP request is a prefetch call.

```php
var_dump($requester->isPrefetch());
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

**wantsJson**

Determine if the request is asking for JSON.

```php
var_dump($requester->wantsJson());
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

**acceptHeader**

Returns the accept header instance.

```php
use Tobento\Service\Requester\AcceptHeader;
use Tobento\Service\Requester\AcceptHeaderItem;

var_dump($requester->acceptHeader() instanceof AcceptHeader);
// bool(true)

var_dump($requester->acceptHeader()->has('application/json'));
// bool(true)

var_dump($requester->acceptHeader()->get('application/json'));
// null|AcceptHeaderItem

// returns all items.
$items = $requester->acceptHeader()->all();

// returns the first item found or null.
$firstItem = $requester->acceptHeader()->first();

// returns true if first item is application/json, otherwise false.
$requester->acceptHeader()->firstIs('application/json');

// filter items returning a new instance.
$acceptHeader = $requester->acceptHeader()->filter(
    fn(AcceptHeaderItem $a): bool => $a->quality() > 0.5
);

// sort items returning a new instance.
$acceptHeader = $requester->acceptHeader()->sort(
    fn(AcceptHeaderItem $a, AcceptHeaderItem $b) => $b->quality() <=> $a->quality()
);

// sorts by highest quality returning a new instance.
$acceptHeader = $requester->acceptHeader()->sortByQuality();
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)