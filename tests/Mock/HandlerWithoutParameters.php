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
 * HandlerWithoutParameters
 */
class HandlerWithoutParameters implements ThrowableHandlerInterface
{    
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