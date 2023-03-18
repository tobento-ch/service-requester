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

namespace Tobento\Service\Requester;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tobento\Service\Collection\Collection;
use JsonSerializable;
use JsonException;
use Traversable;

/**
 * Requester
 */
class Requester implements RequesterInterface
{
    /**
     * @var null|AcceptHeader
     */
    protected null|AcceptHeader $acceptHeader = null;
    
    /**
     * Create a new Requester.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(
        protected ServerRequestInterface $request
    ) {}

    /**
     * Returns the server request.
     * 
     * @return ServerRequestInterface
     */
    public function request(): ServerRequestInterface
    {
        return $this->request;
    }
    
    /**
     * Returns the HTTP method in uppercase such as GET, POST, PUT...
     * 
     * @return string
     */
    public function method(): string
    {
        return strtoupper($this->request->getMethod());
    }
    
    /**
     * Returns the Uri.
     * 
     * @return UriInterface
     */
    public function uri(): UriInterface
    {
        return $this->request->getUri();
    }
    
    /**
     * Returns whether the request is secure or not.
     * 
     * @return bool True if it is secure, else false.
     */
    public function isSecure(): bool
    {
        $https = $this->request->getServerParams()['HTTPS'] ?? null;
        
        return $https !== null && 'off' !== strtolower($https);
    }

    /**
     * Determine if the request is of the specified content type.
     *
     * @param string $contentType The content type such as 'application/json'
     * @return bool
     */
    public function isContentType(string $contentType): bool
    {
        $requestContentType = $this->request->getHeaderLine('Content-Type');

        if (empty($requestContentType)) {
            return false;
        }
        
        return strstr($requestContentType, $requestContentType) === $contentType;
    }

    /**
     * Check if request was via AJAX.
     * 
     * @return bool True on ajax call, otherwise false.
     */
    public function isAjax(): bool
    {
        return $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->isContentType('application/json');
    }
    
    /**
     * Determine if the request is asking for JSON.
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        return $this->acceptHeader()->firstIs(mime: 'application/json');
    }

    /**
     * Returns the JSON payload.
     * 
     * @return Collection
     */
    public function json(): Collection
    {
        if (! $this->isJson()) {
            return new Collection();
        }
        
        $collection = $this->toCollection($this->request->getParsedBody());
        
        if (! is_null($collection)) {
            return $collection;   
        }
        
        try {
            return new Collection(
                json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (JsonException $e) {
            return new Collection();
        }
    }
    
    /**
     * Returns the input data.
     * 
     * @return Collection
     */
    public function input(): Collection
    {
        if ($this->isJson()) {
            return $this->json();
        }
        
        if (in_array($this->method(), ['HEAD', 'GET', 'OPTIONS'])) {
            return new Collection($this->request->getQueryParams());
        }
        
        $collection = $this->toCollection($this->request->getParsedBody());
        
        return $collection ?: new Collection();
    }
    
    /**
     * Returns the accept header instance.
     *
     * @return AcceptHeader
     */
    public function acceptHeader(): AcceptHeader
    {
        if (is_null($this->acceptHeader)) {
            $this->acceptHeader = new AcceptHeader($this->request->getHeaderLine('Accept'));
        }
        
        return $this->acceptHeader;
    }
    
    /**
     * To Collection.
     * 
     * @param mixed $data
     * @return null|Collection
     */
    protected function toCollection(mixed $data): null|Collection
    {
        if (is_array($data)) {
            return new Collection($data);
        }
        
        if ($data instanceof JsonSerializable) {   
            return new Collection((array)$data->jsonSerialize());
        }
        
        if ($data instanceof Traversable) {   
            return new Collection(iterator_to_array($data));
        }        
        
        return null;
    }
}