<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Bridge;

use EFrane\TusBundle\Middleware\MiddlewareCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TusPhp\Tus\Server;

class ServerBridge implements ServerBridgeInterface
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MiddlewareCollection
     */
    private $middlewareCollection;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MiddlewareCollection $middlewareCollection,
        Server $server)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->middlewareCollection = $middlewareCollection;
        $this->server = $server;

        $this->configure();
    }

    public function configure(): void
    {
        $this->server->setDispatcher($this->eventDispatcher);

        foreach ($this->middlewareCollection->all() as $middleware) {
            $this->server->middleware()->add($middleware);
        }
    }

    public function getServer(): Server
    {
        return $this->server;
    }
}
