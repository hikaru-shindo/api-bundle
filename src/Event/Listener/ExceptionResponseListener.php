<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Saikootau\ApiBundle\Resource\Builder\ErrorResourceBuilder;
use Saikootau\ApiBundle\Resource\Builder\ServiceResourceBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionResponseListener extends MediaTypeListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if ($event->getResponse()) {
            // Another listener already added a response, skip
            return;
        }

        if (($request = $event->getRequest()) && $request instanceof Request) {
            $response = $this->getResponse($request, $event->getException());
            $event->setResponse($response);
        }
    }

    private function getResponse(Request $request, Throwable $exception): Response
    {
        $errors = (new ErrorResourceBuilder())->build($exception);
        $service = (new ServiceResourceBuilder($request))
            ->addError(...$errors)
            ->build();

        $serializer = $this->getSerializer($request);

        return new Response(
            $serializer->serialize($service),
            $this->getResponseStatusCodeByException($exception),
            $this->getResponseHeadersByException($serializer->getContentType(), $exception)
        );
    }

    private function getResponseStatusCodeByException(Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getResponseHeadersByException(string $contentType, Throwable $exception): array
    {
        $headers = [];
        if ($exception instanceof HttpExceptionInterface) {
            $headers = array_merge($headers, $exception->getHeaders());
        }
        $headers['Content-Type'] = $contentType;

        return $headers;
    }
}
