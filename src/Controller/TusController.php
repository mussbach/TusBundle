<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Controller;

use EFrane\TusBundle\Bridge\ServerBridge;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class TusController
{
    /**
     * @return Response|BinaryFileResponse
     */
    public function tusAction(ServerBridge $serverBridge)
    {
        return $serverBridge->getServer()->serve();
    }
}
