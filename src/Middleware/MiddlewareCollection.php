<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Middleware;


use TusPhp\Middleware\Middleware;

class MiddlewareCollection
{
    /** @var array<int,Middleware> */
    private $middlewares;

    /**
     * MiddlewareCollection constructor.
     * @param array<Middleware> $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @return array<int,Middleware>
     */
    public function all(): array
    {
        return $this->middlewares;
    }
}
