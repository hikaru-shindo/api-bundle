<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Event\Listener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Saikootau\ApiBundle\Event\Listener\ResourceResponseListener;
use Saikootau\ApiBundle\Http\ResourceResponse;
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

class ResourceResponseListenerTest extends TestCase
{
    public function testSkipsEventIfResponseIsControllerResult(): void
    {
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class)->reveal();

        $listener = new ResourceResponseListener(
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

        $listener = new ResourceResponseListener(
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

        $listener = new ResourceResponseListener(
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

    public function testStatusCodeForPostAndPutIsCorrect(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ResourceResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $postEvent = $this->prophesize(GetResponseForControllerResultEvent::class);
        $postEvent->getRequest()->willReturn(Request::create('/', Request::METHOD_POST, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $postEvent->getResponse()->willReturn(null);
        $postEvent->getControllerResult()->willReturn(new stdClass());
        $postEvent->setResponse(Argument::that(function (Response $response) {
            return Response::HTTP_ACCEPTED === $response->getStatusCode();
        }))->shouldBeCalled();

        $putEvent = $this->prophesize(GetResponseForControllerResultEvent::class);
        $putEvent->getRequest()->willReturn(Request::create('/', Request::METHOD_POST, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $putEvent->getResponse()->willReturn(null);
        $putEvent->getControllerResult()->willReturn(new stdClass());
        $putEvent->setResponse(Argument::that(function (Response $response) {
            return Response::HTTP_ACCEPTED === $response->getStatusCode();
        }))->shouldBeCalled();

        $listener->onKernelView($postEvent->reveal());
        $listener->onKernelView($putEvent->reveal());
    }

    public function testResourceResponseIsConvertedCorrectly(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ResourceResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $response = new ResourceResponse(new stdClass(), Response::HTTP_NOT_ACCEPTABLE, ['X-Test' => 'Test']);

        $event = $this->prophesize(GetResponseForControllerResultEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_POST, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getControllerResult()->willReturn($response);
        $event->setResponse(Argument::that(function (Response $response) {
            $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
            $this->assertSame('Test', $response->headers->get('X-Test'));

            return true;
        }))->shouldBeCalled();

        $listener->onKernelView($event->reveal());
    }
}
