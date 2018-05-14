<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Saikootau\ApiBundle\Resource\Builder\ErrorResourceBuilder;
use Saikootau\ApiBundle\Resource\Builder\ServiceResourceBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
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
            Response::HTTP_INTERNAL_SERVER_ERROR,
            ['Content-Type' => $serializer->getContentType()]
        );
    }
}
