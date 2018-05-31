<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Serializer;

use Saikootau\ApiBundle\MediaType\MediaTypeHandler;
use Saikootau\ApiBundle\MediaType\MediaTypes;

class JsonSerializer extends AbstractSerializer implements MediaTypeHandler
{
    private const FORMAT_JSON = 'json';

    /** {@inheritdoc} */
    public function getSupportedFormat(): string
    {
        return self::FORMAT_JSON;
    }

    /** {@inheritdoc} */
    public function getSupportedMediaTypes(): array
    {
        return [MediaTypes::TYPE_APPLICATION_JSON];
    }

    /** {@inheritdoc} */
    public function getContentType(): string
    {
        return MediaTypes::TYPE_APPLICATION_JSON;
    }
}
