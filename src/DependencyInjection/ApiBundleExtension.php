<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ApiBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'saikootau_api';
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->loadServices($container);

        $configs = $this->loadConfiguration($configs, $container);
        $this->configureResponseListenerDefaultContentType('saikootau_api.event.listener.resource_response', $configs, $container);
        $this->configureResponseListenerDefaultContentType('saikootau_api.event.listener.exception_response', $configs, $container);
    }

    /**
     * Load the service configuration.
     *
     * @param ContainerBuilder $container
     */
    private function loadServices(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/..'));
        $loader->load('Resources/config/services.xml');
    }

    /**
     * Load the bundle configuration. Returns the normalized config.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function loadConfiguration(array $configs, ContainerBuilder $container): array
    {
        $configuration = $this->getConfiguration($configs, $container);

        return $this->processConfiguration($configuration, $configs);
    }

    /**
     * Set the default content type for given response listener service.
     *
     * @param string $serviceId
     * @param array $configs
     * @param ContainerBuilder $container
     */
    private function configureResponseListenerDefaultContentType(string $serviceId, array $configs, ContainerBuilder $container): void
    {
        $responseListener = $container->getDefinition($serviceId);
        $responseListener->setArgument(1, $configs['default_content_type']);
    }
}
