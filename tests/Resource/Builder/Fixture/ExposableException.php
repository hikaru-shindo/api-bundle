<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource\Builder\Fixture;

use Exception;
use Saikootau\ApiBundle\Exception\ExposableError;

class ExposableException extends Exception implements ExposableError
{
    public function getShowName(): string
    {
        return 'TestError';
    }
}
