<?php declare(strict_types=1);

namespace WyriHaximus\React\Cache;

use Psr\Log\LoggerInterface;
use React\Cache\CacheInterface;
use React\Promise\PromiseInterface;
use WyriHaximus\PSR3\CallableThrowableLogger\CallableThrowableLogger;
use function React\Promise\resolve;

final class Swallow implements CacheInterface
{
    private CacheInterface $cache;

    private LoggerInterface $logger;

    public function __construct(CacheInterface $cache, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @param  string           $key
     * @param  null             $default
     * @return PromiseInterface
     */
    public function get($key, $default = null)
    {
        return $this->swallow($this->cache->get($key, $default));
    }

    /**
     * @param  string           $key
     * @param  mixed            $value
     * @param  null             $ttl
     * @return PromiseInterface
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->swallow($this->cache->set($key, $value, $ttl));
    }

    /**
     * @param  string           $key
     * @return PromiseInterface
     */
    public function delete($key)
    {
        return $this->swallow($this->cache->delete($key));
    }

    public function getMultiple(array $keys, $default = null)
    {
        return $this->swallow($this->cache->getMultiple($keys, $default));
    }

    public function setMultiple(array $values, $ttl = null)
    {
        return $this->swallow($this->cache->setMultiple($values, $ttl));
    }

    public function deleteMultiple(array $keys)
    {
        return $this->swallow($this->cache->deleteMultiple($keys));
    }

    public function clear()
    {
        return $this->swallow($this->cache->clear());
    }

    public function has($key)
    {
        return $this->swallow($this->cache->has($key));
    }

    private function swallow(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then(null, function (\Throwable $throwable) {
            CallableThrowableLogger::create($this->logger)($throwable);

            return resolve(null);
        });
    }
}
