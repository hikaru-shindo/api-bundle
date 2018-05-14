<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource\Builder;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Builder\RequestResourceBuilder;
use Saikootau\ApiBundle\Resource\Header;
use Saikootau\ApiBundle\Resource\Request;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestResourceBuilderTest extends TestCase
{
    public function testRequestIsCreated(): void
    {
        $builder = new RequestResourceBuilder();
        $httpRequest = HttpRequest::create('http://localhost:8888/test?q=foo', 'POST');

        $request = $builder->build($httpRequest);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('http://localhost:8888/test?q=foo', $request->getUri());
        $this->assertContainsOnly(Header::class, $request->getHeaders());
    }
}
