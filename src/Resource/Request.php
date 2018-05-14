<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("request")
 */
class Request
{
    /**
     * @Serializer\Type("string")
     * @Serializer\XmlAttribute
     *
     * @var string
     */
    private $method;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlAttribute
     *
     * @var string
     */
    private $uri;

    /**
     * @Serializer\XmlList(
     *     inline=true,
     *     entry="header"
     * )
     * @Serializer\Type("array<Saikootau\ApiBundle\Resource\Header>")
     *
     * @var Header[]
     */
    private $headers;

    public function __construct(string $method, string $uri, Header ...$headers)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
    }

    /**
     * Returns the request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the uri that was requested.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Returns headers associated with this request.
     *
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
