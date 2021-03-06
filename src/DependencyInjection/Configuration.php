<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('saikootau_api');

        $rootNode
            ->children()
                ->scalarNode('default_content_type')
                    ->defaultValue('application/xml')
                    ->info('Which content type to fallback if negotation fails.')
                ->end()
                ->booleanNode('expose_all_errors')
                    ->defaultFalse()
                    ->info('Whether to expose the whole error stack or just some chosen messages for public use.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
