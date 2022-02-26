<?php

namespace EFrane\TusBundle\Bridge;

use EFrane\TusBundle\Exception\NativeCacheStoreException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use TusPhp\Cache\Cacheable;

/**
 * TusPhp Cache Adapter to use the Symfony Cache directly.
 */
class NativeCacheStore implements Cacheable
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array<string,callable>
     */
    private $keyCallables = [];

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->ttl = 300;
    }

    /**
     * @param string $key
     * @param bool   $withExpired
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key, bool $withExpired = false)
    {
        if (!array_key_exists($key, $this->keyCallables)) {
            throw NativeCacheStoreException::missingKey($key);
        }

        return $this->cache->get($this->prefix.$key, $this->keyCallables[$key]);
    }

    public function set(string $key, $value)
    {
        $this->keyCallables[$key] = static function (CacheItemInterface $item) use ($value) {
            $item->expiresAfter($this->ttl);

            return $value;
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteAll(array $keys): bool
    {
        return array_reduce(
            $this->keys(),
            function ($cond, $key) {
                return $cond && $this->delete($key);
            },
            true
        );
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function keys(): array
    {
        return array_keys($this->keyCallables);
    }

    public function setPrefix(string $prefix): Cacheable
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
