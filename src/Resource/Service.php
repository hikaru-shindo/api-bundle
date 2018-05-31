<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource;

use DateTimeImmutable;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot(name="service")
 */
class Service
{
    public const DEFAULT_VERSION = '1.0';

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $version = self::DEFAULT_VERSION;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:sP'>")
     *
     * @var DateTimeInterface
     */
    private $timestamp;

    /**
     * @Serializer\Type("Saikootau\ApiBundle\Resource\Request")
     *
     * @var Request
     */
    private $request;

    /**
     * @Serializer\XmlList(
     *     inline=true,
     *     entry="error"
     * )
     * @Serializer\SerializedName("errors")
     * @Serializer\Type("array<Saikootau\ApiBundle\Resource\Error>")
     *
     * @var Error[]
     */
    private $errors;

    public function __construct(Request $request, Error ...$errors)
    {
        $this->errors = $errors;
        $this->request = $request;
        $this->timestamp = new DateTimeImmutable();
    }

    /**
     * Returns the api version of this response.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set the api version for this response.
     *
     * @param string $version
     *
     * @return Service
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Returns the timestamp this response was created at.
     *
     * @return DateTimeInterface
     */
    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * Returns the request associated with this response.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns a list of errors associated with this response.
     *
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
