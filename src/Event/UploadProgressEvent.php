<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <efrane@meanderingsoul.com>
 */

namespace EFrane\TusBundle\Event;


class UploadProgressEvent extends Event
{
    public const NAME = 'tus.upload.progress';
}
