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
 * ThrowableHandlers
 */
class ThrowableHandlers implements ThrowableHandlersInterface
{
    /**
     * @var array<int, ThrowableHandlerRegistry>
     */    
    protected array $handlers = [];
    
    /**
     * Create a new ThrowableHandlers.
     *
     * @param ThrowableHandlerFactoryInterface $throwableHandlerFactory
     */
    public function __construct(
        protected ThrowableHandlerFactoryInterface $throwableHandlerFactory
    ) {}
        
    /**
     * Add a throwable handler.
     *
     * @param mixed $handler
     * @return ThrowableHandlerRegistry
     */
    public function add(mixed $handler): ThrowableHandlerRegistry
    {
        return $this->handlers[] = new ThrowableHandlerRegistry($handler);
    }
    
    /**
     * Returns the handlers.
     *
     * @return array<int, ThrowableHandlerRegistry>
     */
    public function all(): array
    {
        return $this->handlers;
    }

    /**
     * Handle a throwable.
     *
     * @param Throwable $t
     * @return mixed
     */
    public function handleThrowable(Throwable $t): mixed
    {
        $handlers = $this->handlers;
        
        usort(
            $handlers,
            fn (ThrowableHandlerRegistry $a, ThrowableHandlerRegistry $b): int
                => $b->getPriority() <=> $a->getPriority()
        );
        
        foreach($handlers as $handler)
        {
            if (! $handler->canHandle($t)) {
                continue;    
            }

            try {
                $t = $this->throwableHandlerFactory
                          ->createThrowableHandler($handler->getHandler())
                          ->handle($t);
            } catch (Throwable $t) {
                //
            }

            if (! $t instanceof Throwable) {
                return $t;
            }
        }        
        
        return $t;    
    }    
}