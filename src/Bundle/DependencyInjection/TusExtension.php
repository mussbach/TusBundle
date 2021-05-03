<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bundle\DependencyInjection;

use EFrane\TusBundle\Bridge\ServerBridge;
use EFrane\TusBundle\Bridge\ServerBridgeInterface;
use EFrane\TusBundle\Controller\TusController;
use EFrane\TusBundle\Middleware\MiddlewareCollection;
use EFrane\TusBundle\Routing\RouteLoader;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TusPhp\Cache\FileStore;
use TusPhp\Middleware\TusMiddleware;
use TusPhp\Tus\Server;

class TusExtension extends Extension
{
    /**
     * @param array<string,mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $parsedConfiguration = $this->processConfiguration($configuration, $configs);

        $container->addDefinitions($this->getTusServiceDefinitions($container, $parsedConfiguration));
    }

    /**
     * @param array<string,mixed> $configuration The parsed configuration for the bundle
     *
     * @return array<string, Definition>
     */
    private function getTusServiceDefinitions(ContainerBuilder $containerBuilder, array $configuration): array
    {
        $definitions = [];

        $this->registerController($definitions);
        $this->registerMiddleware($definitions);
        $this->registerRouteLoader($configuration['api_path'], $definitions);
        $this->registerServerBridge($definitions);
        $this->registerTus($configuration, $definitions);

        $containerBuilder->registerForAutoconfiguration(TusMiddleware::class)->addTag('tus.middleware');
        $containerBuilder->registerForAutoconfiguration(ServerBridgeInterface::class);
        $containerBuilder->setAlias(ServerBridgeInterface::class, new Alias(ServerBridge::class));

        return $definitions;
    }

    /**
     * @param array<string,Definition> $definitions
     */
    private function registerRouteLoader(string $apiPath, array &$definitions): void
    {
        $routeLoader = new Definition(RouteLoader::class);
        $routeLoader->setArgument('$apiPath', $apiPath);
        $routeLoader->addTag('routing.loader');

        $definitions[RouteLoader::class] = $routeLoader;
    }

    /**
     * @param array<string,mixed>      $configuration
     * @param array<string,Definition> $definitions
     */
    private function registerTus(array $configuration, array &$definitions): void
    {
        $fileStore = new Definition(FileStore::class, [
            '$cacheDir' => $configuration['cache_dir'],
        ]);
        $fileStore->setLazy(true);

        $definitions[FileStore::class] = $fileStore;

        $server = new Definition(Server::class, [
            '$cacheAdapter' => $fileStore,
        ]);

        $server->addMethodCall('setUploadDir', [$configuration['upload_dir']]);
        $server->addMethodCall('setApiPath', [$configuration['api_path']]);
        $server->setLazy(true);

        $definitions[Server::class] = $server;
    }

    /**
     * @param array<string,Definition> $definitions
     */
    private function registerController(array &$definitions): void
    {
        $controller = new Definition(TusController::class);
        $controller->addTag('controller.service_arguments');
        $controller->setLazy(true);

        $definitions[TusController::class] = $controller;
    }

    /**
     * @param array<string,Definition> $definitions
     */
    private function registerMiddleware(array &$definitions): void
    {
        $middlewareCollection = new Definition(MiddlewareCollection::class);
        $middlewareCollection->setLazy(true);

        $definitions[MiddlewareCollection::class] = $middlewareCollection;
    }

    /**
     * @param array<string,Definition> $definitions
     */
    private function registerServerBridge(array &$definitions): void
    {
        $serverBridge = new Definition(ServerBridge::class);
        $serverBridge->setLazy(true);

        $definitions[ServerBridge::class] = $serverBridge;

        $interface = new Definition(ServerBridgeInterface::class);
        $interface->setClass(ServerBridge::class);

        $definitions[ServerBridgeInterface::class] = $interface;
    }
}
