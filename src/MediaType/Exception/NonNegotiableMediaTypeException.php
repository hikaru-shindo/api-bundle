<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\MediaType\Exception;

use Throwable;

class NonNegotiableMediaTypeException extends MediaTypeException
{
    public function __construct(string $unknownMediaType, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Type \'%s\' is not supported', $unknownMediaType),
            self::CODE,
            $previous
        );
    }
}
