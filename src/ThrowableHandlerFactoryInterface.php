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

/**
 * ThrowableHandlerFactoryInterface
 */
interface ThrowableHandlerFactoryInterface
{
    /**
     * Create throwable handler.
     *
     * @param mixed $handler
     * @return ThrowableHandlerInterface
     */
    public function createThrowableHandler(mixed $handler): ThrowableHandlerInterface;
}