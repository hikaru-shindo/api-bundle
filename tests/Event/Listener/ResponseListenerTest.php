<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Event\Listener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Saikootau\ApiBundle\Event\Listener\ResponseListener;
use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeHandler;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Serializer\Serializer;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class ResponseListenerTest extends TestCase
{
    public function testSkipsEventIfResponseIsControllerResult(): void
    {
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class)->reveal();

        $listener = new ResponseListener(
            $mediaTypeNegotiator,
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForControllerResultEvent::class);
        $event->getControllerResult()->willReturn($this->prophesize(Response::class));
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $listener->onKernelView($event->reveal());
    }

    public function testWillThrowExceptionForUnsupportedMediaType(): void
    {
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(MediaTypes::TYPE_APPLICATION_JSON)->willThrow(new NonNegotiableMediaTypeException('application/json'));
        $mediaTypeNegotiator->getSupportedMediaTypes()->willReturn([MediaTypes::TYPE_APPLICATION_XML]);

        $listener = new ResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForControllerResultEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/json;q=0.8',
        ]));
        $event->getControllerResult()->willReturn(new stdClass());

        $this->expectException(NotAcceptableHttpException::class);
        $listener->onKernelView($event->reveal());
    }

    public function testWillFallBackToDefaultContentTypeOnAcceptedTypeAny(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(MediaTypes::TYPE_APPLICATION_XML)->shouldBeCalledTimes(1)->willReturn($serializer);

        $listener = new ResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForControllerResultEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => '*/*;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getControllerResult()->willReturn(new stdClass());
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $listener->onKernelView($event->reveal());
    }
}
