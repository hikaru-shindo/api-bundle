<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Serializer;

use JMS\Serializer\Serializer as JMSSerializer;

abstract class AbstractSerializer implements Serializer
{
    private $serializer;

    public function __construct(JMSSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Serialize the document to a format.
     *
     * @param object $document
     *
     * @return string
     */
    public function serialize(object $document): string
    {
        return $this->serializer->serialize(
            $document,
            $this->getSupportedFormat()
        );
    }

    /**
     * Deserialize the document to a PHP object of the given class.
     *
     * @param string $document
     * @param string $className
     *
     * @return object
     */
    public function deserialize(string $document, string $className): object
    {
        return $this->serializer->deserialize(
            $document,
            $className,
            $this->getSupportedFormat()
        );
    }

    /**
     * Return the supported format by the serializer.
     *
     * @return string
     */
    abstract public function getSupportedFormat(): string;
}
