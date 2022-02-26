<?php

namespace EFrane\TusBundle\Tests\Bundle;

use EFrane\TusBundle\Bundle\TusBundle;
use Nyholm\BundleTest\BaseBundleTestCase;

abstract class BaseBundleTest extends BaseBundleTestCase
{
    protected function getBundleClass(): string
    {
        return TusBundle::class;
    }

    protected function getBootedKernel(): \Nyholm\BundleTest\AppKernel
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }
}
