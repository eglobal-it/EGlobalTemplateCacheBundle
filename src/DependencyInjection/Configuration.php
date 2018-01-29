<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $rootNode
            ->children()
                ->append($this->createLocalesNode())
                ->append($this->createDirectoriesNode())
                ->scalarNode('cache_dir')->isRequired()->end()
                ->scalarNode('public_prefix')->defaultNull()->end()
                ->booleanNode('exposed_routes_only')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function createLocalesNode(): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition('locales'))
            ->isRequired()
            ->prototype('scalar')->end()
            ->beforeNormalization()
                ->ifString()->then(function ($v) {
                    return preg_split('/\s*,\s*/', $v);
                })
            ->end()
        ;
    }

    private function createDirectoriesNode(): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition('root_dirs'))
            ->prototype('scalar')->end()
            ->beforeNormalization()
                ->ifString()->then(function ($v) {
                    return preg_split('/\s*,\s*/', $v);
                })
            ->end()
        ;
    }
}
