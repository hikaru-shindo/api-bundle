<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\ApiBundle;
use Saikootau\ApiBundle\DependencyInjection\SaikootauApiExtension;

class ApiBundleTest extends TestCase
{
    public function testExtensionIsReturned(): void
    {
        $bundle = new ApiBundle();

        $this->assertInstanceOf(SaikootauApiExtension::class, $bundle->getContainerExtension());
    }
}
