<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Header;
use Saikootau\ApiBundle\Resource\Request;

class RequestTest extends TestCase
{
    public function testObjectInitializesCorrectly(): void
    {
        $header = $this->prophesize(Header::class)->reveal();
        $request = new Request('GET', '/test', $header);

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/test', $request->getUri());
        $this->assertCount(1, $request->getHeaders());
        $this->assertContains($header, $request->getHeaders());
    }
}
