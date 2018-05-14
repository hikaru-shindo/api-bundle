<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

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
            $body = $serializer->serialize($controllerResult);
        }

        $event->setResponse(
            $this->createResponse(
                $body,
                Response::HTTP_OK,
                $serializer->getContentType()
            )
        );
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
     * Create a response for the given data.
     *
     * @param string $body
     * @param int    $status
     * @param string $contentType
     *
     * @return Response
     */
    private function createResponse(string $body, int $status, string $contentType): Response
    {
        return new Response(
            $body,
            $status,
            ['Content-Type' => $contentType]
        );
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
