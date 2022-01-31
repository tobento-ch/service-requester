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

use Tobento\Service\ErrorHandler\ThrowableHandlerInterface;
use Tobento\Service\ErrorHandler\FatalException;
use Tobento\Service\View\ViewInterface;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;
use ErrorException;
use Throwable;

/**
 * Errors
 */
class Errors implements ThrowableHandlerInterface
{
    /**
     * @var array The error types to ignore.
     */
    protected array $ignoredErrors = [
        \E_WARNING,
        \E_USER_WARNING,
        \E_NOTICE,
        \E_DEPRECATED,
        \E_USER_NOTICE,
        \E_USER_DEPRECATED,
    ];
    
    /**
     * Create a new Errors.
     *
     * @param null|ViewInterface $view
     */
    public function __construct(
        protected null|ViewInterface $view = null,
    ) {}
    
    /**
     * Handle a throwable.
     *
     * @param Throwable $t
     * @return mixed Return throwable if cannot handle, otherwise anything.
     */
    public function handle(Throwable $t): mixed
    {
        if (in_array($t->getCode(), $this->ignoredErrors)) {
            return null;
        }
        
        if (
            is_null($this->view)
            || ! $this->view->exists('error')
        ) {
            $this->view = new View(
                new PhpRenderer(
                    new Dirs(
                        new Dir(__DIR__.'/../../resources/view'),
                    )
                )
            );
        }
        
        // remove all previously set headers
        header_remove();
        
        // Important: clean any output as not show it to end user.
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // create header
        header('HTTP/1.1 500 Internal Server Error');
        
        echo $this->view->render('error', ['throwable' => $t]);
        
        exit(1);
    }
}