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
use Tobento\Service\ErrorHandler\ThrowableHandlerRegistry;

/**
 * ThrowableHandlerRegistryTest
 */
class ThrowableHandlerRegistryTest extends TestCase
{
    public function testGetHandlerMethod()
    {
        $registry = new ThrowableHandlerRegistry('handler');
        
        $this->assertSame(
            'handler',
            $registry->getHandler()
        );
    }
    
    public function testGetPriorityMethod()
    {
        $registry = new ThrowableHandlerRegistry('handler', 1500);
        
        $this->assertSame(
            1500,
            $registry->getPriority()
        );
        
        $registry = new ThrowableHandlerRegistry('handler', 1500);
        $registry->priority(2000);
        
        $this->assertSame(
            2000,
            $registry->getPriority()
        );        
    }
    
    public function testCanHandleMethod()
    {
        $registry = new ThrowableHandlerRegistry('handler');
        
        $this->assertTrue(
            $registry->canHandle(new \ErrorException())
        );
    }
    
    public function testCanHandleMethodWithLevels()
    {
        $registry = new ThrowableHandlerRegistry('handler');
        $registry->levels(E_ERROR);
        
        $this->assertTrue(
            $registry->canHandle(new \ErrorException('m', 1))
        );
        
        $registry = new ThrowableHandlerRegistry('handler');
        $registry->levels(E_WARNING);
        
        $this->assertFalse(
            $registry->canHandle(new \ErrorException('m', 1))
        );        
    }
    
    public function testCanHandleMethodWithHandles()
    {
        $registry = new ThrowableHandlerRegistry('handler');
        $registry->handles(\RuntimeException::class);
        
        $this->assertTrue(
            $registry->canHandle(new \RuntimeException())
        );
        
        $registry = new ThrowableHandlerRegistry('handler');
        $registry->handles(\RuntimeException::class);
        
        $this->assertFalse(
            $registry->canHandle(new \ErrorException())
        );        
    }
    
    public function testCanHandleMethodWithLevelsAndHandles()
    {
        $registry = new ThrowableHandlerRegistry('handler');
        
        $registry->levels(E_ERROR);
        $registry->handles(\RuntimeException::class);
        
        $this->assertTrue(
            $registry->canHandle(new \RuntimeException())
        );
                
        $this->assertTrue(
            $registry->canHandle(new \ErrorException('m', 1))
        );
        
        $this->assertFalse(
            $registry->canHandle(new \InvalidArgumentException())
        );        
    }    
}