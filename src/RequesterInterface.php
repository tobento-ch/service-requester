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

/**
 * RequesterInterface
 */
interface RequesterInterface
{
    /**
     * Returns the server request.
     * 
     * @return ServerRequestInterface
     */
    public function request(): ServerRequestInterface;
    
    /**
     * Returns the HTTP method in uppercase such as GET, POST, PUT...
     * 
     * @return string
     */
    public function method(): string;
    
    /**
     * Returns the Uri.
     * 
     * @return UriInterface
     */
    public function uri(): UriInterface;
    
    /**
     * Returns whether the request is secure or not.
     * 
     * @return bool True if it is secure, else false.
     */
    public function isSecure(): bool;

    /**
     * Determine if the request is of the specified content type.
     *
     * @param string $contentType The content type such as 'application/json'
     * @return bool
     */
    public function isContentType(string $contentType): bool;
    
    /**
     * Determine if the HTTP request is a reading request.
     *
     * @return bool
     */
    public function isReading(): bool;
    
    /**
     * Determine if the HTTP request is a prefetch call.
     *
     * @return bool
     */
    public function isPrefetch(): bool;

    /**
     * Check if request was via AJAX.
     * 
     * @return bool True on ajax call, otherwise false.
     */
    public function isAjax(): bool;

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson(): bool;
    
    /**
     * Determine if the request is asking for JSON.
     *
     * @return bool
     */
    public function wantsJson(): bool;

    /**
     * Returns the JSON payload.
     * 
     * @return Collection
     */
    public function json(): Collection;
    
    /**
     * Returns the input data.
     * 
     * @return Collection
     */
    public function input(): Collection;
    
    /**
     * Returns the accept header instance.
     *
     * @return AcceptHeader
     */
    public function acceptHeader(): AcceptHeader;
}