<?php

namespace WH\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package WH\CmsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder
            ->root('wh_cms', 'array')
            ->children()
            ->arrayNode('templates')
            ->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('backendController')->end()
            ->scalarNode('frontController')->end()
            ->scalarNode('frontView')->end()
            ->scalarNode('updateConfig')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('menus')
            ->prototype('scalar')
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
