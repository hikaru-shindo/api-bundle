<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("error")
 */
class Error
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $type;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("code")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $code;

    /**
     * @Serializer\XmlValue
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $message;

    public function __construct(string $type, string $message, ?string $code = null)
    {
        $this->type = $type;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Return the error type. Normally the exception name.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return an error code, if present.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Returns the message for this error.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
