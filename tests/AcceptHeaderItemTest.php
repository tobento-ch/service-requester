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

namespace Tobento\Service\Requester\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Requester\AcceptHeaderItem;

/**
 * AcceptHeaderItemTest
 */
class AcceptHeaderItemTest extends TestCase
{
    public function testItem()
    {
        $item = new AcceptHeaderItem(
            mime: 'text/html',
            quality: 0.5,
        );
        
        $this->assertSame('text/html', $item->mime());
        $this->assertSame(0.5, $item->quality());
        $this->assertSame('text', $item->type());
        $this->assertSame('html', $item->subtype());
    }
    
    public function testWithAnyMimeType()
    {
        $item = new AcceptHeaderItem(
            mime: '*/*',
            quality: 0.5,
        );
        
        $this->assertSame('*/*', $item->mime());
        $this->assertSame(0.5, $item->quality());
        $this->assertSame('*', $item->type());
        $this->assertSame('*', $item->subtype());
    }
    
    public function testWithTypeOnly()
    {
        $item = new AcceptHeaderItem(
            mime: 'image',
            quality: 0.5,
        );
        
        $this->assertSame('image', $item->mime());
        $this->assertSame(0.5, $item->quality());
        $this->assertSame('image', $item->type());
        $this->assertSame('', $item->subtype());
    }
    
    public function testMaxQuality()
    {
        $item = new AcceptHeaderItem(
            mime: 'text/html',
            quality: 1.5,
        );
        
        $this->assertSame(1.0, $item->quality());
    }
    
    public function testMinQuality()
    {
        $item = new AcceptHeaderItem(
            mime: 'text/html',
            quality: -1.5,
        );
        
        $this->assertSame(0.0, $item->quality());
    }    
}