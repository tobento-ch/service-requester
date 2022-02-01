<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Requester\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Requester\Requester;
use Tobento\Service\Requester\RequesterInterface;
use Tobento\Service\Collection\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use JsonSerializable;
use Traversable;
use IteratorAggregate;
use ArrayIterator;

/**
 * RequesterTest
 */
class RequesterTest extends TestCase
{
    public function testThatImplementsRequesterInterface()
    {
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: 'https://example.com',
        );
        
        $this->assertInstanceof(
            RequesterInterface::class,
            new Requester($serverRequest)
        );
    }
    
    public function testRequestMethod()
    {
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: 'https://example.com',
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertTrue($serverRequest === $requester->request());
    }
    
    public function testMethodMethod()
    {
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: 'https://example.com',
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame('GET', $requester->method());
        
        $this->assertSame(
            'POST',
            (new Requester($serverRequest->withMethod('POST')))->method()
        );        
    }    
    
    public function testUriMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertTrue($uri === $requester->uri());
    }
    
    public function testIsSecureMethod()
    {
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: 'https://example.com',
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertFalse($requester->isSecure());
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: 'https://example.com',
            serverParams: ['HTTPS' => 'on'],
        );
        
        $requester = new Requester($serverRequest);        
        
        $this->assertTrue($requester->isSecure());
    }
    
    public function testIsContentTypeMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertFalse($requester->isContentType('application/json'));
        
        $this->assertTrue(
            (new Requester($serverRequest->withAddedHeader('Content-Type', 'application/json')))
                ->isContentType('application/json')
        );
    }
    
    public function testIsAjaxMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertFalse($requester->isAjax());
        
        $this->assertTrue(
            (new Requester($serverRequest->withAddedHeader('X-Requested-With', 'XMLHttpRequest')))
                ->isAjax()
        );
    }
    
    public function testIsJsonMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        );
        
        $requester = new Requester($serverRequest);
        
        $this->assertFalse($requester->isJson());
        
        $this->assertTrue(
            (new Requester($serverRequest->withAddedHeader('Content-Type', 'application/json')))
                ->isJson()
        );
    }
    
    public function testJsonMethodIfNotJsonReturnsEmptyCollection()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        );
        
        $serverRequest = $serverRequest->withParsedBody(['foo' => 'foo']);
        
        $requester = new Requester($serverRequest);
        
        $this->assertInstanceof(
            Collection::class,
            $requester->json()
        );
        
        $this->assertSame(
            [],
            $requester->json()->all()
        );
    }
    
    public function testJsonMethodWithArrayBody()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        )->withParsedBody(['foo' => 'foo'])
         ->withAddedHeader('Content-Type', 'application/json');
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->json()->all()
        );
    }
    
    public function testJsonMethodWithObjectBodyImplementingJsonSerializable()
    {
        $body = new class() implements JsonSerializable {

            public function jsonSerialize() {
                return ['foo' => 'foo'];
            }
        };
        
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        )->withParsedBody($body)
         ->withAddedHeader('Content-Type', 'application/json');
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->json()->all()
        );
    }
    
    public function testJsonMethodWithObjectBodyImplementingTraversable()
    {
        $body = new class() implements IteratorAggregate {

            public function getIterator() {
                return new ArrayIterator(['foo' => 'foo']);
            }
        };
        
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        )->withParsedBody($body)
         ->withAddedHeader('Content-Type', 'application/json');
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->json()->all()
        );
    }
    
    public function testInputMethodWithGetMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        )->withParsedBody(['foo' => 'foo'])
         ->withQueryParams(['bar' => 'bar']);
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['bar' => 'bar'],
            $requester->input()->all()
        );
    }
    
    public function testInputMethodWithGetMethodJson()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'GET',
            uri: $uri,
        )->withParsedBody(['foo' => 'foo'])
         ->withQueryParams(['bar' => 'bar'])
         ->withAddedHeader('Content-Type', 'application/json');
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->input()->all()
        );
    }
    
    public function testInputMethodWithPostMethod()
    {
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'POST',
            uri: $uri,
        )->withParsedBody(['foo' => 'foo'])
         ->withQueryParams(['bar' => 'bar']);
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->input()->all()
        );
    }
    
    public function testInputMethodWithPostMethodWithBodyImplementingJsonSerializable()
    {
        $body = new class() implements JsonSerializable {

            public function jsonSerialize() {
                return ['foo' => 'foo'];
            }
        };
        
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'POST',
            uri: $uri,
        )->withParsedBody($body);
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->input()->all()
        );
    }
    
    public function testInputMethodWithPostMethodWithBodyImplementingTraversable()
    {
        $body = new class() implements IteratorAggregate {

            public function getIterator() {
                return new ArrayIterator(['foo' => 'foo']);
            }
        };
        
        $uri = (new Psr17Factory())->createUri(
            uri: 'https://example.com',
        );
        
        $serverRequest = (new Psr17Factory())->createServerRequest(
            method: 'POST',
            uri: $uri,
        )->withParsedBody($body);
        
        $requester = new Requester($serverRequest);
        
        $this->assertSame(
            ['foo' => 'foo'],
            $requester->input()->all()
        );
    }     
}