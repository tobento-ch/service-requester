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

namespace Tobento\Service\Requester;

/**
 * AcceptHeaderItem
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept
 * @see https://developer.mozilla.org/en-US/docs/Glossary/Quality_values
 */
class AcceptHeaderItem
{
    /**
     * @var string
     */
    protected string $type;
    
    /**
     * @var string
     */
    protected string $subtype;
    
    /**
     * Create a new AcceptHeaderItem.
     *
     * @param string $mime
     * @param float $quality
     */
    public function __construct(
        protected string $mime,
        protected float $quality = 1.0,
    ) {
        $parts = explode('/', $mime);
        $this->type = $parts[0] ?? '';
        $this->subtype = $parts[1] ?? '';
        $this->quality = min(max($quality, 0), 1);
    }

    /**
     * Returns the mime.
     * 
     * @return string
     */
    public function mime(): string
    {
        return $this->mime;
    }
    
    /**
     * Returns the quality.
     * 
     * @return float
     */
    public function quality(): float
    {
        return $this->quality;
    }

    /**
     * Returns the type.
     * 
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }
    
    /**
     * Returns the subtype.
     * 
     * @return string
     */
    public function subtype(): string
    {
        return $this->subtype;
    }
}