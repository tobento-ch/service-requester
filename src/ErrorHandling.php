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

use ErrorException;
use Throwable;

/**
 * ErrorHandling
 */
class ErrorHandling
{
    /**
     * Create a new ErrorHandling.
     *
     * @param ThrowableHandlersInterface $throwableHandlers
     */
    public function __construct(
        protected ThrowableHandlersInterface $throwableHandlers
    ) {}

    /**
     * Registers default shutdown, error and exception handler.
     *
     * @return void
     * @psalm-suppress InvalidArgument
     */
    public function register(): void
    {
        register_shutdown_function([$this, 'handleShutdown']);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * Handle shutdown.
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if (!empty($error)) {
            $exception = new FatalException(
                $error['message'],
                $error['type'],
                0,
                $error['file'],
                $error['line']
            );
            
            $this->throwableHandlers->handleThrowable($exception);
        }
    }

    /**
     * Handle error.
     *
     * @param int $code
     * @param string $message
     * @param string $filename
     * @param int $line
     * @return void
     *
     * @throws Throwable
     */
    public function handleError(int $code, string $message, string $filename = '', int $line = 0): void
    {
        if (!(error_reporting() & $code)) {
            return;
        }
        
        throw new ErrorException($message, $code, 0, $filename, $line);
    }

    /**
     * Handle exception.
     *
     * @param Throwable $t
     * @return void
     */
    public function handleException(Throwable $t): void
    {
        $this->throwableHandlers->handleThrowable($t);
    }
}