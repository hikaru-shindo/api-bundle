<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\MediaType;

interface MediaTypeHandler
{
    /**
     * Return a list of supported media types.
     *
     * @return string[]
     */
    public function getSupportedMediaTypes(): array;
}
