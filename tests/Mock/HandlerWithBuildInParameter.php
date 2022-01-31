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
 * HandlerWithBuildInParameter
 */
class HandlerWithBuildInParameter implements ThrowableHandlerInterface
{
    /**
     * Create a new HandlerWithBuildInParameter
     *
     * @param Foo $foo
     * @param int $number
     */
    public function __construct(
        protected Foo $foo,
        protected int $number,
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