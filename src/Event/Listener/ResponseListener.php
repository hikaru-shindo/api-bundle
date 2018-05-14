<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Saikootau\ApiBundle\Http\ResourceResponse;
use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class ResponseListener
{
    private $defaultContentType;
    private $mediaTypeNegotiator;

    private static $noContentMethods = [
        'HEAD',
    ];

    public function __construct(MediaTypeNegotiator $mediaTypeNegotiator, string $defaultContentType)
    {
        $this->mediaTypeNegotiator = $mediaTypeNegotiator;
        $this->defaultContentType = $defaultContentType;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Response) {
            return;
        }

        $serializer = $this->getSerializer($event->getRequest());
        $body = '';
        if ($this->isContentAllowed($event->getRequest()->getMethod())) {
            $body = $serializer->serialize($this->getResource($controllerResult));
        }

        $event->setResponse(new Response(
            $body,
            $this->getStatusCode($event->getRequest(), $controllerResult),
            $this->getHeaders($controllerResult, $serializer->getContentType())
        ));
    }

    /**
     * Get the actual resource from the controller result.
     *
     * @param object $controllerResult
     *
     * @return object
     */
    private function getResource(object $controllerResult): object
    {
        if ($controllerResult instanceof ResourceResponse) {
            return $controllerResult->getResource();
        }

        return $controllerResult;
    }

    /**
     * Get the status code to return from the controller result.
     * Defaults to 202 for POST, PUT and 200 for every other method.
     *
     * @param Request $request
     * @param object  $controllerResult
     *
     * @return int
     */
    private function getStatusCode(Request $request, object $controllerResult): int
    {
        if ($controllerResult instanceof ResourceResponse) {
            return $controllerResult->getStatusCode();
        }

        return in_array($request->getMethod(), ['POST', 'PUT']) ? Response::HTTP_ACCEPTED : Response::HTTP_OK;
    }

    /**
     * Generates an array of headers for the given controller result.
     *
     * @param object $controllerResult
     * @param string $contentType
     *
     * @return array
     */
    private function getHeaders(object $controllerResult, string $contentType): array
    {
        $headers = ['Content-Type' => $contentType];
        if ($controllerResult instanceof ResourceResponse) {
            $headers = array_merge($controllerResult->getHeaders(), $headers);
        }

        return $headers;
    }

    /**
     * Checks if the response should contain any content.
     *
     * @param string $method
     *
     * @return bool
     */
    private function isContentAllowed(string $method): bool
    {
        return !in_array($method, self::$noContentMethods);
    }

    /**
     * Get a matching serializer for the given request's acceptable content types.
     *
     * @param Request $request
     *
     * @return Serializer
     *
     * @throws NotAcceptableHttpException
     */
    protected function getSerializer(Request $request): Serializer
    {
        try {
            /** @var Serializer $serializer */
            $serializer = $this->mediaTypeNegotiator->negotiate(...$this->getRequestAcceptableContentTypes($request));
        } catch (NonNegotiableMediaTypeException $exception) {
            throw new NotAcceptableHttpException(
                sprintf(
                    'Deliverable content types are: %s',
                    implode(', ', $this->mediaTypeNegotiator->getSupportedMediaTypes())
                )
            );
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
