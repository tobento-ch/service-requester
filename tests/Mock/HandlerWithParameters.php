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

namespace Tobento\Service\ErrorHandler\Test\Mock;

use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;
use Throwable;

/**
 * HandlerWithParameters
 */
class HandlerWithParameters implements ThrowableHandlerInterface
{
    /**
     * Create a new HandlerWithParameters
     *
     * @param Foo $foo
     */
    public function __construct(
        protected Foo $foo,
    ) {}
    
    /**
     * Handle a throwable.
     *
     * @param Throwable $t
     * @return mixed Return throwable if cannot handle, otherwise anything.
     */
    public function handle(Throwable $t): mixed
    {
        return $t;
    }
}