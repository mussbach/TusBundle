<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Routing;


use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    /**
     * @var string
     */
    private $apiPath;

    public function __construct(string $apiPath)
    {
        $this->apiPath = $apiPath;
    }

    /**
     * @param mixed       $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return 'tus_bundle' === $type;
    }

    public function load($resource, $type = null): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add('tus_upload', new Route(
            $this->apiPath,
            [
                '_controller' => 'EFrane\TusBundle\Controller\TusController::tusAction',
            ],
            [], [], '', [],
            [
                'POST',
            ]
        ));

        $routes->add('tus_token', new Route(
            "{$this->apiPath}/{token?}",
            [
                '_controller' => 'EFrane\TusBundle\Controller\TusController::tusAction',
            ],
            [
                'token' => '.+',
            ],
            [], '', [],
            [
                'POST',
                'HEAD',
                'PATCH',
            ]
        ));

        return $routes;
    }
}
