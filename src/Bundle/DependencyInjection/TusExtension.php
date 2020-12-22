<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bundle\DependencyInjection;


use EFrane\TusBundle\Bridge\ServerBridge;
use EFrane\TusBundle\Controller\TusController;
use EFrane\TusBundle\Middleware\MiddlewareCollection;
use EFrane\TusBundle\Routing\RouteLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use TusPhp\Cache\FileStore;
use TusPhp\Middleware\TusMiddleware;
use TusPhp\Tus\Server;

class TusExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $parsedConfiguration = $this->processConfiguration($configuration, $configs);

        $container->addDefinitions($this->getTusServiceDefinitions($container, $parsedConfiguration));
    }

    /**
     * @param array<string,mixed> $configuration The parsed configuration for the bundle
     */
    private function getTusServiceDefinitions(ContainerBuilder $containerBuilder, array $configuration): array
    {
        $definitions = [];

        $this->registerController($definitions);
        $this->registerMiddleware($containerBuilder, $definitions);
        $this->registerRouteLoader($configuration['api_path'], $definitions);
        $this->registerServerBridge($configuration['max_upload_size'], $definitions);
        $this->registerTus($configuration, $definitions);

        return $definitions;
    }

    private function registerRouteLoader($apiPath, array &$definitions): void
    {
        $routeLoader = new Definition(RouteLoader::class);
        $routeLoader->setArgument('$apiPath', $apiPath);
        $routeLoader->addTag('routing.loader');

        $definitions[RouteLoader::class] = $routeLoader;
    }

    private function registerTus($configuration, array &$definitions): void
    {
        $fileStore = new Definition(FileStore::class, [
            '$cacheDir' => $configuration['cache_dir'],
        ]);
        $fileStore->setLazy(true);

        $definitions[FileStore::class] = $fileStore;

        $server = new Definition(Server::class, [
            '$cacheAdapter' => $fileStore
        ]);

        $server->addMethodCall('setUploadDir', [$configuration['upload_dir']]);
        $server->addMethodCall('setApiPath', [$configuration['api_path']]);
        $server->setLazy(true);

        $definitions[Server::class] = $server;
    }

    private function registerController(array &$definitions): void
    {
        $controller = new Definition(TusController::class);
        $controller->addTag('controller.service_arguments');
        $controller->setLazy(true);

        $definitions[TusController::class] = $controller;
    }

    private function registerMiddleware(ContainerBuilder $containerBuilder, array &$definitions)
    {
        $containerBuilder->registerForAutoconfiguration(TusMiddleware::class)->addTag('tus.middleware');

        $middlewareCollection = new Definition(MiddlewareCollection::class);
        $middlewareCollection->setLazy(true);

        $definitions[MiddlewareCollection::class] = $middlewareCollection;
    }

    private function registerServerBridge(int $maxUploadSize, array &$definitions): void
    {
        $serverBridge = new Definition(ServerBridge::class);
        $serverBridge->setAutowired(true);
        $serverBridge->setLazy(true);
        $serverBridge->setArgument('$maxUploadSize', $maxUploadSize);

        $definitions[ServerBridge::class] = $serverBridge;
    }
}
