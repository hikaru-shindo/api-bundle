<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\ApiBundle;
use Saikootau\ApiBundle\DependencyInjection\ApiBundleExtension;

class ApiBundleTest extends TestCase
{
    public function testExtensionIsReturned(): void
    {
        $bundle = new ApiBundle();

        $this->assertInstanceOf(ApiBundleExtension::class, $bundle->getContainerExtension());
    }
}
