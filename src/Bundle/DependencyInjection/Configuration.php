<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tus');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $children = $rootNode->children();

        $cacheConfig = $children->arrayNode('cache_type')
            ->performNoDeepMerging()
            ->validate()
            ->ifTrue(static function (array $cacheConfigs): bool {
                $enabledCacheConfigs = array_filter(
                    $cacheConfigs,
                    static function (array $cacheConfig): bool {
                        return $cacheConfig['enabled'];
                    }
                );

                return 1 !== count($enabledCacheConfigs);
            })
            ->thenInvalid('You can only specify one of the available cache configurations.')
            ->end()
            ->children();

        $children->integerNode('cache_ttl')
            ->info('Set the ttl for cached items')
            ->min(0)
            ->max(PHP_INT_MAX)
            ->defaultValue(300);

        $cacheConfig->arrayNode('apcu')
            ->info('Use apcu for caching')
            ->canBeEnabled();

        $fileCache = $cacheConfig->arrayNode('file')
            ->info('Use a simple file-based cache')
            ->canBeEnabled()
            ->children();

        $fileCache->scalarNode('dir')
            ->info('Directory for cached files')
            ->defaultValue('%kernel.cache_dir%/%kernel.environment%');

        $fileCache->scalarNode('name')
            ->info('Key for the cached files')
            ->defaultValue('tus_php.server.cache');

        $cacheConfig->arrayNode('native')
            ->info('Use the Symfony CacheInterface')
            ->canBeEnabled();

        $redisCache = $cacheConfig->arrayNode('redis')
            ->info('Use redis for caching')
            ->canBeEnabled()
            ->children();

        $redisCache->scalarNode('host')
            ->info('Redis host')
            ->defaultValue('127.0.0.1');

        $redisCache->integerNode('port')
            ->info('Redis port')
            ->defaultValue(6379)
            ->min(1)
            ->max(65535);

        $redisCache->integerNode('db')
            ->info('Redis DB')
            ->defaultValue(0)
            ->min(0)
            ->max(1024);

        $children->integerNode('max_upload_size')
            ->info('Maximum upload size')
            ->min(0)
            ->max(PHP_INT_MAX)
            ->defaultValue(0);

        $children->scalarNode('upload_dir')
            ->info('Directory for finished uploads')
            ->defaultValue('%kernel.project_dir%/public/uploads');

        $children->scalarNode('api_path')
            ->info('Path to the tus api')
            ->defaultValue('/_tus/upload');

        return $treeBuilder;
    }
}
