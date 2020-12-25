<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bridge;


use TusPhp\Tus\Server;

interface ServerBridgeInterface
{
    public function configure(): void;

    public function getServer(): Server;
}
