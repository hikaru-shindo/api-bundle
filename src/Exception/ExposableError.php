<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Exception;

interface ExposableError
{
    public function getShowName(): string;
}
