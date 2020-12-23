<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bundle\DependencyInjection\Compiler;


use EFrane\TusBundle\Middleware\MiddlewareCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TusMiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $middlewares = array_map(
            static function (string $serviceId) {
                return new Reference($serviceId);
            },
            array_keys($container->findTaggedServiceIds('tus.middleware'))
        );

        $middlewareCollection = new Definition(MiddlewareCollection::class);
        $middlewareCollection->setArgument('$middlewares', $middlewares);

        $container->setDefinition(MiddlewareCollection::class, $middlewareCollection);
    }
}
