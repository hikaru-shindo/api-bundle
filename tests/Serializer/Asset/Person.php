<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Serializer\Asset;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Person.
 *
 * @Serializer\XmlRoot("person")
 */
class Person
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $name;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("gender")
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $gender;
}
