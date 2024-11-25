<?php

namespace EFrane\TusBundle\Tests\Bundle;

use EFrane\TusBundle\Bundle\TusBundle;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase as NyholmBaseBundleTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

abstract class BaseBundleTestCase extends NyholmBaseBundleTestCase
{
    protected function getBundleClass(): string
    {
        return TusBundle::class;
    }

    /**
     * @param array<string,Definition> $additionalDefinitions Additional service definitions
     * @return AppKernel
     */
    protected function bootWithAdditionalDefinitions(array $additionalDefinitions = []): AppKernel
    {
        $definitionLoaderPass = new class implements CompilerPassInterface {
            /**
             * @var array<string,Definition>
             */
            private $definitions = [];

            public function process(ContainerBuilder $container): void
            {
                $container->addDefinitions($this->definitions);
            }

            /**
             * @param array<string,Definition> $definitions
             */
            public function setDefinitions(array $definitions): void
            {
                $this->definitions = $definitions;
            }
        };

        $definitionLoaderPass->setDefinitions($additionalDefinitions);

        $kernel = $this->createKernel();

        $kernel->addConfigFile(__DIR__.'/../Resources/config_native.yaml');

        $kernel->addCompilerPasses([$definitionLoaderPass]);
        $kernel->boot();

        return $kernel;
    }
}
