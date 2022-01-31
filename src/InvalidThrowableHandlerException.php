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

use InvalidArgumentException;
use Throwable;

/**
 * InvalidThrowableHandlerException
 */
class InvalidThrowableHandlerException extends InvalidArgumentException
{
    /**
     * Create a new InvalidThrowableHandlerException
     *
     * @param mixed $handler
     * @param string $message The message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected mixed $handler,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        if ($message === '') {
            
            $handler = $this->convertHandlerToString($handler);
            
            $message = 'Throwable Handler ['.$handler.'] is invalid';    
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the handler.
     *
     * @return mixed
     */
    public function handler(): mixed
    {
        return $this->handler;
    }

    /**
     * Convert handler to string.
     *
     * @param mixed $handler
     * @return string
     */
    protected function convertHandlerToString(mixed $handler): string
    {
        if (is_string($handler)) {
            return $handler;
        }
        
        if (is_object($handler)) {
            return $handler::class;
        }
        
        return '';
    }
}