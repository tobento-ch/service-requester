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
 * ThrowableHandlerFactory
 */
class ThrowableHandlerFactory implements ThrowableHandlerFactoryInterface
{
    /**
     * Create throwable handler.
     *
     * @param mixed $handler
     * @return ThrowableHandlerInterface
     *
     * @throws InvalidThrowableHandlerException
     */
    public function createThrowableHandler(mixed $handler): ThrowableHandlerInterface
    {
        // if it is already an instance, just return.
        if ($handler instanceof ThrowableHandlerInterface) {
            return $handler;
        }
        
        if (is_callable($handler)) {
            return $this->createCallableHandler($handler);
        }
        
        $handlerData = [];
            
        if (
            is_array($handler) 
            && isset($handler[0])
            && is_string($handler[0])
        ) {
            $handlerData = $handler;
            
            // remove handler
            array_shift($handlerData);

            $handler = $handler[0];
        }
        
        if (!is_string($handler)) {
            throw new InvalidThrowableHandlerException($handler);
        }
        
        try {
            $handler = new $handler(...$handlerData);
        } catch (Throwable $e) {
            throw new InvalidThrowableHandlerException($handler, $e->getMessage());
        }
        
        if (! $handler instanceof ThrowableHandlerInterface)
        {
            throw new InvalidThrowableHandlerException($handler);
        }
        
        return $handler;
    }
    
    /**
     * Create a callable throwable handler.
     *
     * @param callable $handler
     * @return ThrowableHandlerInterface
     */        
    protected function createCallableHandler(callable $handler): ThrowableHandlerInterface
    {
        return new class ($handler) implements ThrowableHandlerInterface
        {
            public function __construct(
                private $handler,
            ) {}

            public function handle(
                Throwable $t,
            ): null|Throwable {
                return ($this->handler)($t);
            }
        };
    }    
}