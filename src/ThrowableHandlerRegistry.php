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

namespace Tobento\Service\ErrorHandler;

use Throwable;

/**
 * ThrowableHandlerRegistry
 */
class ThrowableHandlerRegistry
{
    /**
     * @var array<int, string> The throwables the handler can handle.
     */
    protected array $handles = [];
    
    /**
     * @var array<int, int> The error levels the handler can handle.
     */
    protected array $levels = [];    
    
    /**
     * Create a new ThrowableHandlerRegistry.
     *
     * @param mixed $handler
     * @param int $priority
     */
    public function __construct(
        protected mixed $handler,
        protected int $priority = 1000,
    ) {}

    /**
     * Returns the handler.
     *    
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }
    
    /**
     * Set the throwables the handler can handle.
     *
     * @param string $throwable
     * @return static $this
     */
    public function handles(string ...$throwable): static
    {
        $this->handles = $throwable;
        return $this;
    }
    
    /**
     * Set the error levels the handler can handle.
     *
     * @param int $level
     * @return static $this
     */
    public function levels(int ...$level): static
    {
        $this->levels = $level;
        return $this;
    }    
    
    /**
     * Set the priority.
     *
     * @param int $priority
     * @return static $this
     */
    public function priority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }
    
    /**
     * Returns true if the handler can handle, otherwise false.
     *    
     * @return bool
     */
    public function canHandle(Throwable $t): bool
    {        
        if (
            !empty($this->levels)
            && in_array($t->getCode(), $this->levels)
        ) {
            return true;
        }
        
        if (
            !empty($this->handles)
            && in_array($t::class, $this->handles)
        ) {
            return true;
        }
        
        // handle all if none isset:
        if (empty($this->levels) && empty($this->handles)) {
            return true;
        }
        
        return false;
    }    
    
    /**
     * Returns the priority.
     *    
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }    
}