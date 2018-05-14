<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Header;

class HeaderTest extends TestCase
{
    public function testObjectInitializesCorrectly(): void
    {
        $header = new Header('foo', 'bar');

        $this->assertSame('foo', $header->getName());
        $this->assertSame('bar', $header->getValue());
    }
}
