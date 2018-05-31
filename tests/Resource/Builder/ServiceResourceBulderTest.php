<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource\Builder;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Builder\ServiceResourceBuilder;
use Saikootau\ApiBundle\Resource\Error;
use Saikootau\ApiBundle\Resource\Request;
use Saikootau\ApiBundle\Resource\Service;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class ServiceResourceBuilderTest extends TestCase
{
    public function testServiceIsCreated(): void
    {
        $request = new HttpRequest();
        $builder = new ServiceResourceBuilder($request);

        $service = $builder->build();

        $this->assertInstanceOf(Service::class, $service);
        $this->assertInstanceOf(Request::class, $service->getRequest());
        $this->assertEmpty($service->getErrors());
    }

    public function testServiceIsCreatedWithErrors(): void
    {
        $request = new HttpRequest();
        $builder = new ServiceResourceBuilder($request);

        $builder->addError(
            $this->prophesize(Error::class)->reveal(),
            $this->prophesize(Error::class)->reveal()
        );

        $service = $builder->build();

        $this->assertCount(2, $service->getErrors());
        $this->assertContainsOnly(Error::class, $service->getErrors());
    }
}
