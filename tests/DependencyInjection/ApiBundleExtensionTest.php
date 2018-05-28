<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\DependencyInjection;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\ApiBundle;
use Saikootau\ApiBundle\Event\Listener\ExceptionResponseListener;
use Saikootau\ApiBundle\Event\Listener\ResourceResponseListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiBundleExtensionTest extends TestCase
{
    public function testEventListenersAreRegisteredCorrectly(): void
    {
        $container = $this->getContainerForConfig([], function (ContainerBuilder $container) {
            $container->getDefinition('saikootau_api.event.listener.exception_response')->setPublic(true);
            $container->getDefinition('saikootau_api.event.listener.resource_response')->setPublic(true);
        });

        $this->assertNotEmpty(
            $container->getDefinition('saikootau_api.event.listener.exception_response')->getTag('kernel.event_listener'),
            sprintf('%s is no event listener', ExceptionResponseListener::class)
        );
        $this->assertNotEmpty(
            $container->getDefinition('saikootau_api.event.listener.resource_response')->getTag('kernel.event_listener'),
            sprintf('%s is no event listener', ResourceResponseListener::class)
        );
    }

    private function getContainerForConfig(array $configs, callable $configurator = null): ContainerBuilder
    {
        $bundle = new ApiBundle();
        $extension = $bundle->getContainerExtension();

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir().'/api');
        $container->setParameter('kernel.bundles', []);
        $container->registerExtension($extension);

        $container->set(
            'jms_serializer',
            $this->prophesize(SerializerInterface::class)->reveal()
        );

        $extension->load($configs, $container);

        $bundle->build($container);

        if ($configurator) {
            $configurator($container);
        }

        $container->compile();

        return $container;
    }
}
