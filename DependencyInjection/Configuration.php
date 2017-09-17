<?php

namespace Mweb\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mweb_admin');

        $rootNode->children()
                ->scalarNode('noIllu')->defaultValue('mwebadminbundle/img/noIllu.jpg')->end()
                ->scalarNode('fileManagerFolder')->defaultValue('uploads/tinymce')->end()
                ->arrayNode('menus')
                    ->useAttributeAsKey('alias')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('beautyName')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entities')
                        ->useAttributeAsKey('alias')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('beautyName')->end()
                                ->scalarNode('showOnHome')->defaultValue('false')->end()
                                ->scalarNode('menu')->end()
                                ->scalarNode('class')->end()
                                ->scalarNode('form')->end()
                                ->scalarNode('views')->defaultValue('default')->end()
                                ->scalarNode('orderBy')->defaultValue('position')->end()
                                ->scalarNode('menuIntegrable')->end()
                                ->scalarNode('unique')
                                    ->defaultValue(false)
                                ->end()
                                ->arrayNode('listProperties')
                                    ->useAttributeAsKey('alias')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('dataName')->end()
                                            ->scalarNode('beautyName')->end()
                                            ->scalarNode('type')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
