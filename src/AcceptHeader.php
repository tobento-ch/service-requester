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

/**
 * AcceptHeader
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept
 */
class AcceptHeader
{
    /**
     * @var array<int, AcceptHeaderItem>
     */
    protected array $items = [];
    
    /**
     * Create a new AcceptHeader.
     *
     * @param string $acceptHeaderLine
     */
    public function __construct(
        protected string $acceptHeaderLine
    ) {
        $this->parseAcceptHeaderLine($acceptHeaderLine);
    }
    
    /**
     * Returns true if has item, otherwise false
     *
     * @param string ...$mimes
     * @return bool
     */
    public function has(string ...$mimes): bool
    {
        foreach($mimes as $mime) {
            if (is_null($this->get(mime: $mime))) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Returns the item by mime or null if not exists.
     *
     * @param string $mime
     * @return null|AcceptHeaderItem
     */
    public function get(string $mime): null|AcceptHeaderItem
    {
        return $this->filter(
            fn(AcceptHeaderItem $a) => $a->mime() === strtolower($mime)
        )->first();
    }
    
    /**
     * Returns all items.
     *
     * @return array<int, AcceptHeaderItem>
     */
    public function all(): array
    {
        return $this->items;
    }
    
    /**
     * Returns the first item or null if none.
     *
     * @return null|AcceptHeaderItem
     */
    public function first(): null|AcceptHeaderItem
    {
        $key = array_key_first($this->items);
        
        if (is_null($key)) {
            return null;
        }
        
        return $this->items[$key];
    }
    
    /**
     * Returns true if the first item is of the specified mime, otherwise false.
     *
     * @param string $mime
     * @return bool
     */
    public function firstIs(string $mime): bool
    {
        return $this->first()?->mime() === $mime;
    }
    
    /**
     * Returns a new instance with the filtered items.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->items = array_filter($this->items, $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with the items sorted.
     *
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callback): static
    {
        $items = $this->all();
        usort($items, $callback);
        
        $new = clone $this;
        $new->items = $items;
        return $new;
    }
    
    /**
     * Returns a new instance with the items sorted by its highest quality.
     *
     * @return static
     */
    public function sortByQuality(): static
    {
        return $this->sort(fn(AcceptHeaderItem $a, AcceptHeaderItem $b) => $b->quality() <=> $a->quality());
    }

    /**
     * Parses the accept header line.
     *
     * @param string $acceptHeaderLine
     * @return void
     */
    protected function parseAcceptHeaderLine(string $acceptHeaderLine): void
    {
        $parts = explode(',', $acceptHeaderLine);
        
        foreach ($parts as $part) {
            
            $part = trim($part);
            
            if ($part !== '') {
                $this->items[] = $this->createAcceptHeaderItem(string: $part);
            }
        }
    }
    
    /**
     * Create the accept header item from string.
     * 
     * @param string $string e.g application/xml;q=0.9
     * @return AcceptHeaderItem
     */
    protected function createAcceptHeaderItem(string $string): AcceptHeaderItem
    {
        $parts = explode(';', $string);
        
        if (!isset($parts[0])) {
            return new AcceptHeaderItem(mime: '');
        }
        
        $mime = strtolower(trim($parts[0]));
        
        if (!isset($parts[1])) {
            return new AcceptHeaderItem(mime: $mime);
        }
        
        $parts = explode('=', trim($parts[1]));
        
        if (!isset($parts[1])) {
            return new AcceptHeaderItem(mime: $mime);
        }        
        
        return new AcceptHeaderItem(mime: $mime, quality: (float)$parts[1]);
    }
}