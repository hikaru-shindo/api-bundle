<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Serializer;

use Saikootau\ApiBundle\MediaType\MediaTypeHandler;
use Saikootau\ApiBundle\MediaType\MediaTypes;

class XmlSerializer extends AbstractSerializer implements MediaTypeHandler
{
    private const FORMAT_XML = 'xml';

    /** {@inheritdoc} */
    public function getSupportedFormat(): string
    {
        return self::FORMAT_XML;
    }

    /** {@inheritdoc} */
    public function getSupportedMediaTypes(): array
    {
        return [MediaTypes::TYPE_APPLICATION_XML];
    }

    /** {@inheritdoc} */
    public function getContentType(): string
    {
        return MediaTypes::TYPE_APPLICATION_XML;
    }
}
