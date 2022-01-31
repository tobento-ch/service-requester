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

namespace Tobento\Service\ErrorHandler\Test\Handler;

use PHPUnit\Framework\TestCase;
use Tobento\Service\ErrorHandler\Handler\Errors;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;

/**
 * ErrorsTest
 */
class ErrorsTest extends TestCase
{
    public function testThatImplementsThrowableHandlerInterface()
    {
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            new Errors()
        );
    }
}