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

namespace Tobento\Service\ErrorHandler\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\ErrorHandler\ThrowableHandlers;
use Tobento\Service\ErrorHandler\ThrowableHandlersInterface;
use Tobento\Service\ErrorHandler\ThrowableHandlerFactory;
use Tobento\Service\ErrorHandler\Test\Mock\{
    HandlerFoo,
    HandlerBar,
};

/**
 * ThrowableHandlersTest
 */
class ThrowableHandlersTest extends TestCase
{
    public function testThatImplementsThrowableHandlersInterface()
    {
        $this->assertInstanceof(
            ThrowableHandlersInterface::class,
            new ThrowableHandlers(new ThrowableHandlerFactory())
        );
    }

    public function testStopsFurtherHandlingIfHandlerDoesNotReturnThrowable()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(new \ErrorException());
        
        $this->assertSame(
            'foo',
            $response
        );
    }
    
    public function testUnmatchedLevelsGetsNotExecuted()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class)->levels(E_WARNING);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException()
        );
        
        $this->assertSame(
            'bar',
            $response
        );
    }
    
    public function testMatchedLevelsGetsExecuted()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class)->levels(E_ERROR);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException('message', 1)
        );
        
        $this->assertSame(
            'foo',
            $response
        );
    }
    
    public function testUnmatchedThrowableGetsNotExecuted()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class)->handles(\RuntimeException::class);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException()
        );
        
        $this->assertSame(
            'bar',
            $response
        );
    }
    
    public function testMatchedThrowableGetsExecuted()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class)->handles(\ErrorException::class);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException()
        );
        
        $this->assertSame(
            'foo',
            $response
        );
    }
    
    public function testPrioritizeHigherGetsFirstExecuted()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class)->priority(1000);
        $throwableHandlers->add(HandlerBar::class)->priority(1500);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException()
        );
        
        $this->assertSame(
            'bar',
            $response
        );
    }
    
    public function testAllMethod()
    {
        $throwableHandlers = new ThrowableHandlers(new ThrowableHandlerFactory());
        
        $throwableHandlers->add(HandlerFoo::class);
        $throwableHandlers->add(HandlerBar::class);
        
        $response = $throwableHandlers->handleThrowable(
            new \ErrorException('message', 1)
        );
        
        $this->assertSame(
            2,
            count($throwableHandlers->all())
        );
    }    
}