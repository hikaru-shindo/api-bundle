<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testConfigTreeIsBuiltCorrectly(): void
    {
        $configuration = new Configuration();

        $this->assertInstanceOf(TreeBuilder::class, $configuration->getConfigTreeBuilder());
    }
}
