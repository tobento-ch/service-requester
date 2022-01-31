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
use Tobento\Service\ErrorHandler\AutowiringThrowableHandlerFactory;
use Tobento\Service\ErrorHandler\InvalidThrowableHandlerException;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;
use Tobento\Service\Container\Container;
use Tobento\Service\ErrorHandler\Test\Mock\{
    HandlerWithParameters,
    HandlerWithBuildInParameter,
    HandlerWithoutParameters,
};

/**
 * AutowiringThrowableHandlerFactoryTest
 */
class AutowiringThrowableHandlerFactoryTest extends TestCase
{
    private function createFactory(): AutowiringThrowableHandlerFactory
    {
        return new AutowiringThrowableHandlerFactory(new Container());
    }
    
    public function testCreateFromString()
    {
        $factory = $this->createFactory();
        
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            $factory->createThrowableHandler(HandlerWithoutParameters::class)
        );
    }
    
    public function testCreateFromArray()
    {
        $factory = $this->createFactory();
        
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            $factory->createThrowableHandler([HandlerWithoutParameters::class])
        );
    }
    
    public function testCreateFromArrayWithParameters()
    {
        $factory = $this->createFactory();
        
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            $factory->createThrowableHandler([HandlerWithBuildInParameter::class, 'number' => 20])
        );
    }
    
    public function testCreateFromCallable()
    {
        $factory = $this->createFactory();
        
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            $factory->createThrowableHandler(function($request, $handler) {
                return $handler->handle($request);
            }));
    }
    
    public function testThatUnresolvableHandlerThrowsInvalidThrowableHandlerException()
    {
        $this->expectException(InvalidThrowableHandlerException::class);
        
        $this->createFactory()->createThrowableHandler('Foo');
    }    
}