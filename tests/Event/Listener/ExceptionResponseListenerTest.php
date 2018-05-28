<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Event\Listener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Saikootau\ApiBundle\Event\Listener\ExceptionResponseListener;
use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeHandler;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionResponseListenerTest extends TestCase
{
    public function testSkipsEventIfNoRequestIsGiven(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(null);
        $event->getResponse()->willReturn(null);
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testSkipsEventIfResponseIsPresent(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn($this->prophesize(Request::class));
        $event->getResponse()->willReturn($this->prophesize(Response::class));
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testConvertsExceptionToResponseIfNoResponseIsGiven(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($this->prophesize(Exception::class));
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testConvertsExceptionToResponseIfNoResponseIsGivenForGivenExposeArgument(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML,
            true
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($this->prophesize(Exception::class));
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testDefaultStatusCodeIsInternalError(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($this->prophesize(Exception::class));
        $event->setResponse(Argument::that(function (Response $response) {
            return Response::HTTP_INTERNAL_SERVER_ERROR === $response->getStatusCode();
        }))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testSetsStatusCodeForHttpException(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn(new HttpException(Response::HTTP_NOT_FOUND));
        $event->setResponse(Argument::that(function (Response $response) {
            return Response::HTTP_NOT_FOUND === $response->getStatusCode();
        }))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testSetsCorrectHeadersForHttpException(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $exception->setHeaders(['X-Test' => 'Test']);

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($exception);
        $event->setResponse(Argument::that(function (Response $response) {
            return 'Test' === $response->headers->get('X-Test');
        }))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testContentTypeIsOverridenForHttpException(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $serializer->method('getContentType')->willReturn('application/xml');

        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(Argument::any())->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $exception->setHeaders(['Content-Type' => 'text/html']);

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/xml;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($exception);
        $event->setResponse(Argument::that(function (Response $response) {
            return 'application/xml' === $response->headers->get('Content-Type');
        }))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testWillFallbackToDefaultContentType(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(MediaTypes::TYPE_APPLICATION_JSON)->willThrow(new NonNegotiableMediaTypeException('application/json'));
        $mediaTypeNegotiator->negotiate(MediaTypes::TYPE_APPLICATION_XML)->shouldBeCalledTimes(1)->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => 'application/json;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($this->prophesize(Exception::class));
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }

    public function testWillFallBackToDefaultContentTypeOnAcceptedTypeAny(): void
    {
        $serializer = $this->getMockBuilder([Serializer::class, MediaTypeHandler::class])->getMock();
        $mediaTypeNegotiator = $this->prophesize(MediaTypeNegotiator::class);
        $mediaTypeNegotiator->negotiate(MediaTypes::TYPE_APPLICATION_XML)->shouldBeCalledTimes(1)->willReturn($serializer);

        $listener = new ExceptionResponseListener(
            $mediaTypeNegotiator->reveal(),
            MediaTypes::TYPE_APPLICATION_XML
        );

        $event = $this->prophesize(GetResponseForExceptionEvent::class);
        $event->getRequest()->willReturn(Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTP_ACCEPT' => '*/*;q=0.8',
        ]));
        $event->getResponse()->willReturn(null);
        $event->getException()->willReturn($this->prophesize(Exception::class));
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $listener->onKernelException($event->reveal());
    }
}
