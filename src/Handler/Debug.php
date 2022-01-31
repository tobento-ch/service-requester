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
 * Debug
 */
class Debug implements ThrowableHandlerInterface
{
    /**
     * @var array
     */    
    protected array $codes = [
        \E_DEPRECATED => 'Deprecated',
        \E_USER_DEPRECATED => 'User Deprecated',
        \E_NOTICE => 'Notice',
        \E_USER_NOTICE => 'User Notice',
        \E_STRICT => 'Runtime Notice',
        \E_WARNING => 'Warning',
        \E_USER_WARNING => 'User Warning',
        \E_COMPILE_WARNING => 'Compile Warning',
        \E_CORE_WARNING => 'Core Warning',
        \E_USER_ERROR => 'User Error',
        \E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        \E_COMPILE_ERROR => 'Compile Error',
        \E_PARSE => 'Parse Error',
        \E_ERROR => 'Error',
        \E_CORE_ERROR => 'Core Error',
    ];
    
    /**
     * Create a new Debug.
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
        if (
            is_null($this->view)
            || ! $this->view->exists('debug/error')
        ) {
            $this->view = new View(
                new PhpRenderer(
                    new Dirs(
                        new Dir(__DIR__.'/../../resources/view'),
                    )
                )
            );
        }

        $this->view->addMacro('toErrorType', [$this, 'toErrorType']);
        $this->view->addMacro('generateCodeLines', [$this, 'generateCodeLines']);
        
        // remove all previously set headers
        header_remove();
        
        // Important: clean any output as not show it to end user.
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // create header
        header('HTTP/1.1 500 Internal Server Error');
        
        echo $this->view->render('debug/error', ['throwable' => $t]);
        
        exit(1);
    }
    
    /**
     * Returns the error type from code.
     *
     * @param int $code
     * @return string
     */
    public function toErrorType(int $code): string
    {
        return $this->codes[$code] ?? 'Unknown Error';
    }
    
    /**
     * Generates the code lines from the throwable.
     *
     * @param null|string $file
     * @param int $line
     * @param int $numberOfLines
     * @return array<int, string>
     */
    public function generateCodeLines(null|string $file, int $line, int $numberOfLines = 10): array
    {
        if (is_null($file)) {
            return [];    
        }
        
        $file = file($file);
        $num = (int) ($numberOfLines / 2);
        $offset = $numberOfLines - ($num * 2);
        $start = ($line - $num >= 0) ? $line - $num : $line - 1;
        $end = $line + $num + $offset;
        $lines = [];

        for ($i = $start; $i < $end; $i++) {
            if (! isset($file[$i])) {
                continue;
            }

            $text = $file[$i];
            
            $lines[$i + 1] = $text;
        }
        
        return $lines;
    }
}