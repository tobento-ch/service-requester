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

namespace Tobento\Service\ErrorHandler\Handler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;
use Tobento\Service\ErrorHandler\FatalException;
use ErrorException;
use Error;
use Throwable;
use Closure;

/**
 * Log
 */
class Log implements ThrowableHandlerInterface
{
    /**
     * @var array
     */
    protected array $logAs = [
        \E_DEPRECATED => LogLevel::INFO,
        \E_USER_DEPRECATED => LogLevel::INFO,
        \E_NOTICE => LogLevel::WARNING,
        \E_USER_NOTICE => LogLevel::WARNING,
        \E_STRICT => LogLevel::WARNING,
        \E_WARNING => LogLevel::WARNING,
        \E_USER_WARNING => LogLevel::WARNING,
        \E_COMPILE_WARNING => LogLevel::WARNING,
        \E_CORE_WARNING => LogLevel::WARNING,
        \E_USER_ERROR => LogLevel::CRITICAL,
        \E_RECOVERABLE_ERROR => LogLevel::CRITICAL,
        \E_COMPILE_ERROR => LogLevel::CRITICAL,
        \E_PARSE => LogLevel::CRITICAL,
        \E_ERROR => LogLevel::CRITICAL,
        \E_CORE_ERROR => LogLevel::CRITICAL,
    ];
    
    /**
     * Create a new Log.
     *
     * @param Closure|LoggerInterface $logger
     */
    public function __construct(
        protected Closure|LoggerInterface $logger,
    ) {}
    
    /**
     * Handle a throwable.
     *
     * @param Throwable $t
     * @return mixed Return throwable if cannot handle, otherwise anything.
     */
    public function handle(Throwable $t): mixed
    {
        $logLevel = $this->logAs[$t->getCode()] ?? LogLevel::INFO;

        if ($t instanceof FatalException) {
            $message = 'Fatal '.$t->getMessage();
        } elseif ($t instanceof Error) {
            $message = 'Uncaught Error: '.$t->getMessage();
        } elseif ($t instanceof ErrorException) {
            $message = 'Uncaught '.$t->getMessage();
        } else {
            $message = 'Uncaught Exception: '.$t->getMessage();
        }

        try {
            
            if ($this->logger instanceof Closure) {
                $this->logger = call_user_func($this->logger);
            }
            
            $this->logger->log($logLevel, $message, ['exception' => $t]);
        } catch (Throwable $e) {
            //
        }
        
        return $t;
    }
}