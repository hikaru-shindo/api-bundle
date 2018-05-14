<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;
use Saikootau\ApiBundle\MediaType\MediaTypeNegotiator;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

abstract class MediaTypeListener
{
    private $defaultContentType;
    private $mediaTypeNegotiator;

    public function __construct(MediaTypeNegotiator $mediaTypeNegotiator, string $defaultContentType)
    {
        $this->mediaTypeNegotiator = $mediaTypeNegotiator;
        $this->defaultContentType = $defaultContentType;
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
