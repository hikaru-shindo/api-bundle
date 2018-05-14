<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Error;

class ErrorTest extends TestCase
{
    public function testObjectInitializesCorrectly(): void
    {
        $error = new Error('TestType', 'foo bar', '1337');

        $this->assertSame('TestType', $error->getType());
        $this->assertSame('foo bar', $error->getMessage());
        $this->assertSame('1337', $error->getCode());
    }

    public function testObjectInitializesCorrectlyWithoutCode(): void
    {
        $error = new Error('TestType', 'foo bar');

        $this->assertSame('TestType', $error->getType());
        $this->assertSame('foo bar', $error->getMessage());
        $this->assertNull($error->getCode());
    }
}
