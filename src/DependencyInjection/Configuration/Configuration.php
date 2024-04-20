<?php

declare(strict_types=1);


namespace Zjk\DtoMapper\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zjk_dto_mapper');
        $rootNode    = $treeBuilder->getRootNode();

        /**
         * @psalm-suppress PossiblyNullReference, PossiblyUndefinedMethod, UndefinedMethod
         * @phpstan-ignore-next-line
         */
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('cache_pool')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
