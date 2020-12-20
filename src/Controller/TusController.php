<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Controller;


use EFrane\TusBundle\Event\Event;
use EFrane\TusBundle\Event\UploadCompleteEvent;
use EFrane\TusBundle\Event\UploadCreatedEvent;
use EFrane\TusBundle\Event\UploadMergedEvent;
use EFrane\TusBundle\Event\UploadProgressEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server;

class TusController
{
    private function createEventHandler(string $eventClass, EventDispatcherInterface $eventDispatcher): callable
    {
        return static function (TusEvent $tusEvent) use ($eventClass, $eventDispatcher) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $eventDispatcher->dispatch(new $eventClass($tusEvent), $eventClass::NAME);
        };
    }

    public function tusAction(EventDispatcherInterface $eventDispatcher, Server $server)
    {
        $server->event()->addListener(
            'tus-server.upload.created',
            $this->createEventHandler(UploadCreatedEvent::class, $eventDispatcher)
        );

        $server->event()->addListener(
            'tus-server.upload.progress',
            $this->createEventHandler(UploadProgressEvent::class, $eventDispatcher)
        );

        $server->event()->addListener(
            'tus-server.upload.complete',
            $this->createEventHandler(UploadCompleteEvent::class, $eventDispatcher)
        );

        $server->event()->addListener(
            'tus-server.upload.merged',
            $this->createEventHandler(UploadMergedEvent::class, $eventDispatcher)
        );

        return $server->serve();
    }
}
