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
use Tobento\Service\Requester\AcceptHeader;
use Tobento\Service\Requester\AcceptHeaderItem;

/**
 * AcceptHeaderTest
 */
class AcceptHeaderTest extends TestCase
{
    public function testIgnoresEmpty()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, ,,,application/xhtml+xml, application/xml;q=0.9,image/webp, */*;q=0.8'
        );
        
        $this->assertSame(5, count($acceptHeader->all()));
    }
    
    public function testParsing()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/xml;q=0.9,*/*;q=0.8'
        );
        
        $this->assertSame(
            ['text/html', 1.0],
            [$acceptHeader->all()[0]?->mime(), $acceptHeader->all()[0]?->quality()],
        );
        
        $this->assertSame(
            ['application/xml', 0.9],
            [$acceptHeader->all()[1]?->mime(), $acceptHeader->all()[1]?->quality()],
        );
        
        $this->assertSame(
            ['*/*', 0.8],
            [$acceptHeader->all()[2]?->mime(), $acceptHeader->all()[2]?->quality()],
        );
    }
    
    public function testHasMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.9, image/webp, */*;q=0.8'
        );
        
        $this->assertTrue($acceptHeader->has('application/json'));
        $this->assertTrue($acceptHeader->has('Application/Json'));
        $this->assertTrue($acceptHeader->has('application/json', 'text/html'));
        $this->assertFalse($acceptHeader->has('application/xml'));
        $this->assertTrue($acceptHeader->has('*/*'));
        $this->assertFalse($acceptHeader->has('application/json', 'application/xml'));
    }
    
    public function testGetMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.9, image/webp, */*;q=0.8'
        );
        
        $this->assertInstanceof(
            AcceptHeaderItem::class,
            $acceptHeader->get('application/json')
        );
        
        $this->assertInstanceof(
            AcceptHeaderItem::class,
            $acceptHeader->get('Application/Json')
        );        
        
        $this->assertNull($acceptHeader->get('application/xml'));
    }
    
    public function testFirstMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.9, image/webp'
        );
        
        $this->assertSame('text/html', $acceptHeader->first()?->mime());
    }
    
    public function testFirstIsMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.9, image/webp'
        );
        
        $this->assertTrue($acceptHeader->firstIs(mime: 'text/html'));
        $this->assertFalse($acceptHeader->firstIs(mime: 'application/json'));
        $this->assertFalse($acceptHeader->firstIs(mime: 'application/xml'));
    }
    
    public function testFilterMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.2, image/webp;q=0.6'
        );
        
        $acceptHeaderNew = $acceptHeader->filter(
            fn(AcceptHeaderItem $a): bool => $a->quality() > 0.5
        );
        
        $this->assertFalse($acceptHeader === $acceptHeaderNew);
        $this->assertSame(2, count($acceptHeaderNew->all()));
    }
    
    public function testSortMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.2, image/webp;q=0.6'
        );
        
        $mimes = [];
        
        foreach($acceptHeader->all() as $item) {
            $mimes[] = $item->mime();
        }
        
        $this->assertSame(
            ['text/html', 'application/json', 'image/webp'],
            $mimes,
        );
        
        $acceptHeaderNew = $acceptHeader->sort(
            fn(AcceptHeaderItem $a, AcceptHeaderItem $b) => $b->quality() <=> $a->quality()
        );
        
        $this->assertFalse($acceptHeader === $acceptHeaderNew);
        
        $mimes = [];
        
        foreach($acceptHeaderNew->all() as $item) {
            $mimes[] = $item->mime();
        }
        
        $this->assertSame(
            ['text/html', 'image/webp', 'application/json'],
            $mimes,
        );
    }
    
    public function testSortByQualityMethod()
    {
        $acceptHeader = new AcceptHeader(
            'text/html, application/json;q=0.2, image/webp;q=0.6'
        );
        
        $mimes = [];
        
        foreach($acceptHeader->all() as $item) {
            $mimes[] = $item->mime();
        }
        
        $this->assertSame(
            ['text/html', 'application/json', 'image/webp'],
            $mimes,
        );
        
        $acceptHeaderNew = $acceptHeader->sortByQuality();
        
        $this->assertFalse($acceptHeader === $acceptHeaderNew);
        
        $mimes = [];
        
        foreach($acceptHeaderNew->all() as $item) {
            $mimes[] = $item->mime();
        }
        
        $this->assertSame(
            ['text/html', 'image/webp', 'application/json'],
            $mimes,
        );
    }
}