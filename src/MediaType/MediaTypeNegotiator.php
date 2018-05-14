<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\MediaType;

use Saikootau\ApiBundle\MediaType\Exception\NonNegotiableMediaTypeException;

class MediaTypeNegotiator
{
    /**
     * @var MediaTypeHandler[]
     */
    private $handlers;

    /**
     * Negotiate a media type handler best matching the given media types.
     *
     * @param string ...$mediaTypes
     *
     * @throws NonNegotiableMediaTypeException
     *
     * @return MediaTypeHandler
     */
    public function negotiate(string ...$mediaTypes): MediaTypeHandler
    {
        foreach ($mediaTypes as $mediaType) {
            foreach ($this->handlers as $handler) {
                if (in_array($mediaType, $handler->getSupportedMediaTypes())) {
                    return $handler;
                }
            }
        }

        throw new NonNegotiableMediaTypeException(implode(', ', $mediaTypes));
    }

    /**
     * Add a list of media type handlers for negotiation.
     *
     * @param MediaTypeHandler ...$handlers
     *
     * @return MediaTypeNegotiator
     */
    public function addHandler(MediaTypeHandler ...$handlers): self
    {
        foreach ($handlers as $handler) {
            $this->handlers[spl_object_hash($handler)] = $handler;
        }

        return $this;
    }
}
