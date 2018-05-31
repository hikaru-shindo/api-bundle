<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("header")
 */
class Header
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlValue
     *
     * @var string
     */
    private $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns the name of the header.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of the header.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
