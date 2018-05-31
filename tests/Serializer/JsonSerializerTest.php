<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Serializer\JsonSerializer;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use Saikootau\ApiBundle\Tests\Serializer\Asset\Person;

class JsonSerializerTest extends TestCase
{
    public function testMediaTypeSupport(): void
    {
        $serializer = new JsonSerializer($this->getSerializer());
        $this->assertEquals('json', $serializer->getSupportedFormat());
        $this->assertContains(MediaTypes::TYPE_APPLICATION_JSON, $serializer->getSupportedMediaTypes());
        $this->assertSame(MediaTypes::TYPE_APPLICATION_JSON, $serializer->getContentType());
    }

    public function testJsonFormatSerialization(): void
    {
        $expectedSerialization = '{"name":"tester","gender":"male"}';
        $person = new Person();
        $person->name = 'tester';
        $person->gender = 'male';
        $serializer = new JsonSerializer($this->getSerializer());
        $this->assertEquals($expectedSerialization, $serializer->serialize($person));
    }

    public function testJsonFormatDeserialization(): void
    {
        $document = '{"name":"tester","gender":"male"}';
        $serializer = new JsonSerializer($this->getSerializer());
        $person = $serializer->deserialize($document, Person::class);
        $this->assertInstanceOf(Person::class, $person);
    }

    private function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }
}
