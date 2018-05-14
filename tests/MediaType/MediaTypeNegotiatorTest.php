<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\NeduaType;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeHandler;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;

class MediaTypeNegotiatorTest extends TestCase
{
    /** @test */
    public function negotiationWorks(): void
    {
        $negotiator = new MediaTypeNegotiator();
        $handlerProphecy = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy->getSupportedMediaTypes()->willReturn(['foo']);

        $handler = $handlerProphecy->reveal();
        $negotiator->addHandler($handler);

        $this->assertEquals($handler, $negotiator->negotiate('foo'));
    }

    /** @test */
    public function negotiationGetsFirstHandler(): void
    {
        $negotiator = new MediaTypeNegotiator();
        $handlerProphecy1 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy2 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy3 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy1->getSupportedMediaTypes()->willReturn(['bar']);
        $handlerProphecy2->getSupportedMediaTypes()->willReturn(['foo']);
        $handlerProphecy3->getSupportedMediaTypes()->willReturn(['foo']);

        $handler1 = $handlerProphecy1->reveal();
        $handler2 = $handlerProphecy2->reveal();
        $handler3 = $handlerProphecy3->reveal();
        $negotiator->addHandler($handler1, $handler2, $handler3);

        $this->assertEquals($handler2, $negotiator->negotiate('foo'));
    }

    /** @test */
    public function unknownMediaTypeThrowsException(): void
    {
        $negotiator = new MediaTypeNegotiator();
        $handlerProphecy = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy->getSupportedMediaTypes()->willReturn(['foo']);

        $handler = $handlerProphecy->reveal();
        $negotiator->addHandler($handler);

        $this->expectException(NonNegotiableMediaTypeException::class);
        $negotiator->negotiate('bar');
    }

    /** @test */
    public function shouldReturnArrayOfSupportedTypes(): void
    {
        $negotiator = new MediaTypeNegotiator();
        $handlerProphecy1 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy2 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy3 = $this->prophesize(MediaTypeHandler::class);
        $handlerProphecy1->getSupportedMediaTypes()->willReturn(['bar']);
        $handlerProphecy2->getSupportedMediaTypes()->willReturn(['foo']);
        $handlerProphecy3->getSupportedMediaTypes()->willReturn(['foo']);

        $handler1 = $handlerProphecy1->reveal();
        $handler2 = $handlerProphecy2->reveal();
        $handler3 = $handlerProphecy3->reveal();
        $negotiator->addHandler($handler1, $handler2, $handler3);

        $this->assertEquals(['foo', 'bar'], $negotiator->getSupportedMediaTypes());
    }
}
