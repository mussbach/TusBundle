<?php

namespace EFrane\TusBundle\Exception;

use RuntimeException;

class NativeCacheStoreException extends RuntimeException
{
    public static function missingKey(string $key): self
    {
        return new self("Key '{$key}' has not been cached");
    }
}
