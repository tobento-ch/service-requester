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
use Tobento\Service\ErrorHandler\Handler\Log;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

/**
 * LogTest
 */
class LogTest extends TestCase
{
    public function testThatImplementsThrowableHandlerInterface()
    {
        $logger = new Logger('name');
        
        $this->assertInstanceof(
            ThrowableHandlerInterface::class,
            new Log($logger)
        );
    }
    
    public function testLogWarning()
    {
        $logger = new Logger('name');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $handler = new Log($logger);
        
        $handler->handle(new \ErrorException('message', 2));
        
        $this->assertTrue($testHandler->hasRecords('warning'));
    }
    
    public function testLogCritical()
    {
        $logger = new Logger('name');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $handler = new Log($logger);
        
        $handler->handle(new \ErrorException('message', 1));
        
        $this->assertTrue($testHandler->hasRecords('critical'));
    }
    
    public function testLoggerAsClosure()
    {
        $testHandler = new TestHandler();
        
        $handler = new Log(function() use ($testHandler) {
            $logger = new Logger('name');
            $logger->pushHandler($testHandler);   
            return $logger;
        });
        
        $handler->handle(new \ErrorException('message', 2));
        
        $this->assertTrue($testHandler->hasRecords('warning'));
    }    
}