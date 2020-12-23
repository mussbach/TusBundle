<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Tests\Bundle\DependencyInjection;


use EFrane\TusBundle\Bundle\DependencyInjection\TusExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class TusExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new TusExtension(),
        ];
    }

    public function testExtensionCanLoad(): void
    {
        $this->load();

        self::assertTrue($this->container->hasExtension('tus'));
    }
}
