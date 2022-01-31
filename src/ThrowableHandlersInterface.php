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
 * ThrowableHandlersInterface
 */
interface ThrowableHandlersInterface
{
    /**
     * Add a throwable handler.
     *
     * @param mixed $handler
     * @return ThrowableHandlerRegistry
     */
    public function add(mixed $handler): ThrowableHandlerRegistry;

    /**
     * Returns the handlers.
     *
     * @return array<int, ThrowableHandlerRegistry>
     */
    public function all(): array;
    
    /**
     * Handle a throwable.
     *
     * @param Throwable $t
     * @return mixed
     */
    public function handleThrowable(Throwable $t): mixed;
}