<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Serializer;

interface Serializer
{
    /**
     * Serialize the document to a format.
     *
     * @param object $document
     *
     * @return string
     */
    public function serialize(object $document): string;

    /**
     * Deserialize the document to a PHP object of the given class.
     *
     * @param string $document
     * @param string $className
     *
     * @return object
     */
    public function deserialize(string $document, string $className): object;

    /**
     * Get the content type for the serializer.
     *
     * @return string
     */
    public function getContentType(): string;
}
