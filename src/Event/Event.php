<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Event;


use Symfony\Contracts\EventDispatcher\Event as SymfonyEvent;
use TusPhp\Events\TusEvent;

abstract class Event extends SymfonyEvent
{
    /**
     * @var TusEvent
     */
    protected $originalEvent;

    public function __construct(TusEvent $uploadCreated)
    {
        $this->originalEvent = $uploadCreated;
    }

    public function getOriginalEvent(): TusEvent
    {
        return $this->originalEvent;
    }
}
