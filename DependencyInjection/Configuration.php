<?php

namespace Mattsches\VersionEyeBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mattsches_version_eye');
        $rootNode
            ->children()
            ->scalarNode('api_key')
                ->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('base_url')
                ->isRequired()
                ->cannotBeEmpty()
                ->defaultValue('https://www.versioneye.com/api/v1')
                ->end()
            ->scalarNode('filesystem_cache_path')
                ->isRequired()
                ->cannotBeEmpty()
                ->defaultValue('%kernel.cache_dir%/versioneye')
                ->end()
            ->end()
            ;
        return $treeBuilder;
    }
}
