<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource\Builder;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Builder\HeaderResourceBuilder;
use Saikootau\ApiBundle\Resource\Header;
use Symfony\Component\HttpFoundation\Request;

class HeaderResourceBuilderTest extends TestCase
{
    public function testHeadersAreCreated(): void
    {
        $builder = new HeaderResourceBuilder();
        $request = new Request();
        $request->headers->set('Content-Type', ['application/json']);
        $request->headers->set('Host', ['localhost']);
        $request->headers->set('X-Test', ['test1', 'test2']);

        $headers = $builder->build($request);

        $this->assertCount(4, $headers);
        $this->assertContainsOnly(Header::class, $headers);
    }

    public function testHeaderIsCreatedCorrectly(): void
    {
        $builder = new HeaderResourceBuilder();
        $request = new Request();
        $request->headers->set('Content-Type', ['application/json']);

        $headers = $builder->build($request);

        $this->assertCount(1, $headers);
        $this->assertContainsOnly(Header::class, $headers);
        $this->assertRegExp('/^content\-type$/i', $headers[0]->getName());
        $this->assertSame('application/json', $headers[0]->getValue());
    }
}
