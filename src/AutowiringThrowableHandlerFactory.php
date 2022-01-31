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

use Psr\Container\ContainerInterface;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;

/**
 * AutowiringThrowableHandlerFactory
 */
class AutowiringThrowableHandlerFactory extends ThrowableHandlerFactory
{
    /**
     * @var Autowire
     */    
    private Autowire $autowire;
    
    /**
     * Create a new AutowiringThrowableHandlerFactory.
     *
     * @param ContainerInterface $container
     */    
    public function __construct(
        ContainerInterface $container
    ) {
        $this->autowire = new Autowire($container);
    }
    
    /**
     * Create throwable handler.
     *
     * @param mixed $handler
     * @return ThrowableHandlerInterface
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
            $handler = $this->autowire->resolve($handler, $handlerData);
        } catch (AutowireException $e) {
            throw new InvalidThrowableHandlerException($handler, $e->getMessage());
        }
        
        if (! $handler instanceof ThrowableHandlerInterface)
        {
            throw new InvalidThrowableHandlerException($handler);
        }
        
        return $handler;
    }
}