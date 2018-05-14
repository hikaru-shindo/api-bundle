<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Resource\Builder\ErrorResourceBuilder;
use Saikootau\ApiBundle\Resource\Builder\ServiceResourceBuilder;
use Saikootau\ApiBundle\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionResponseListener
{
    private $defaultContentType;
    private $mediaTypeNegotiator;

    public function __construct(MediaTypeNegotiator $mediaTypeNegotiator, string $defaultContentType)
    {
        $this->mediaTypeNegotiator = $mediaTypeNegotiator;
        $this->defaultContentType = $defaultContentType;
    }

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

    /**
     * Get a matching serializer for the given request's acceptable content types.
     *
     * @param Request $request
     *
     * @return Serializer
     *
     * @throws NonNegotiableMediaTypeException
     */
    protected function getSerializer(Request $request): Serializer
    {
        try {
            /** @var Serializer $serializer */
            $serializer = $this->mediaTypeNegotiator->negotiate(...$this->getRequestAcceptableContentTypes($request));
        } catch (NonNegotiableMediaTypeException $exception) {
            /** @var Serializer $serializer */
            $serializer = $this->mediaTypeNegotiator->negotiate($this->defaultContentType);
        }

        return $serializer;
    }

    /**
     * Returns an array of acceptable content types for the given request.
     *
     * @param Request $request
     *
     * @return string[]
     */
    private function getRequestAcceptableContentTypes(Request $request): array
    {
        $acceptableContentTypes = $request->getAcceptableContentTypes();

        if (false !== ($anyIndex = array_search(MediaTypes::TYPE_APPLICATION_ANY, $acceptableContentTypes))) {
            $acceptableContentTypes[$anyIndex] = $this->defaultContentType;
        }

        return $acceptableContentTypes;
    }
}
