<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use Saikootau\ApiBundle\Serializer\XmlSerializer;
use Saikootau\ApiBundle\MediaType\MediaTypes;
use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Tests\Serializer\Asset\Person;

class XmlSerializerTest extends TestCase
{
    public function testMediaTypeSupport()
    {
        $serializer = new XmlSerializer($this->getSerializer());
        $this->assertEquals('xml', $serializer->getSupportedFormat());
        $this->assertContains(MediaTypes::TYPE_APPLICATION_XML, $serializer->getSupportedMediaTypes());
        $this->assertSame(MediaTypes::TYPE_APPLICATION_XML, $serializer->getContentType());
    }

    public function testXmlFormatSerialization()
    {
        $expectedSerialization = '<?xml version="1.0" encoding="UTF-8"?>';
        $expectedSerialization .= "\n<person name=\"tester\" gender=\"male\"/>\n";
        $person = new Person();
        $person->name = 'tester';
        $person->gender = 'male';
        $serializer = new XmlSerializer($this->getSerializer());
        $this->assertEquals($expectedSerialization, $serializer->serialize($person));
    }

    public function testXmlFormatDeserialization()
    {
        $document = $this->getDocumentFixture();
        $serializer = new XmlSerializer($this->getSerializer());
        $person = $serializer->deserialize($document->saveXML(), Person::class);
        $this->assertInstanceOf(Person::class, $person);
    }

    public function testXmlFormatDeserializationWithAnDOMDocument()
    {
        $document = $this->getDocumentFixture()->saveXML();
        $serializer = new XmlSerializer($this->getSerializer());
        $person = $serializer->deserialize($document, Person::class);
        $this->assertInstanceOf(Person::class, $person);
    }

    private function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }

    private function getDocumentFixture()
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $personElement = $document->appendChild($document->createElement('person'));
        $nameAttribute = $personElement->appendChild($document->createAttribute('name'));
        $nameAttribute->nodeValue = 'tester';
        $genderAttribute = $personElement->appendChild($document->createAttribute('gender'));
        $genderAttribute->nodeValue = 'male';

        return $document;
    }
}
