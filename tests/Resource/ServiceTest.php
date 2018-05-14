<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Error;
use Saikootau\ApiBundle\Resource\Request;
use Saikootau\ApiBundle\Resource\Service;

class ServiceTest extends TestCase
{
    public function testInitializesWithOneError(): void
    {
        $request = $this->prophesize(Request::class)->reveal();
        $error = $this->prophesize(Error::class)->reveal();

        $service = new Service($request, $error);

        $this->assertSame(Service::DEFAULT_VERSION, $service->getVersion());
        $this->assertInstanceOf(DateTimeInterface::class, $service->getTimestamp());
        $this->assertSame($request, $service->getRequest());
        $this->assertCount(1, $service->getErrors());
        $this->assertContains($error, $service->getErrors());
    }

    public function testInitializesWithMultipleErrors(): void
    {
        $request = $this->prophesize(Request::class)->reveal();
        $error = $this->prophesize(Error::class)->reveal();
        $error2 = $this->prophesize(Error::class)->reveal();

        $service = new Service($request, $error, $error2);

        $this->assertSame(Service::DEFAULT_VERSION, $service->getVersion());
        $this->assertInstanceOf(DateTimeInterface::class, $service->getTimestamp());
        $this->assertSame($request, $service->getRequest());
        $this->assertCount(2, $service->getErrors());
        $this->assertContains($error, $service->getErrors());
        $this->assertContains($error2, $service->getErrors());
    }

    public function testVersionCanBeChanged(): void
    {
        $request = $this->prophesize(Request::class)->reveal();
        $error = $this->prophesize(Error::class)->reveal();

        $service = new Service($request, $error);

        $service->setVersion('2.0');
        $this->assertSame('2.0', $service->getVersion());
    }
}
