<?php

namespace Lighthouse;

use Lighthouse\Common\ResultCodes;
use Lighthouse\Core\CacheableInterface;
use Lighthouse\Core\ServiceInterface;
use Lighthouse\Core\StorageInterface;
use Lighthouse\Core\ValidatorInterface;

class CachedService implements ServiceInterface
{
    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @var ValidatorInterface
     */
    private $queryValidator;

    public function __construct(ServiceInterface $service, StorageInterface $cache)
    {
        $this->service = $service;
        $this->cache = $cache;
    }

    /**
     * @param string $infoHash
     *
     * @return Result
     */
    public function get($infoHash)
    {
        return $this->service->get($infoHash);
    }

    /**
     * @param Query $query
     *
     * @return Result
     */
    public function search(Query $query)
    {
        return $this->service->search($query);
    }

    /**
     * @param Torrent $torrent
     *
     * @return Result
     */
    public function put(Torrent $torrent)
    {
        if ($this->isUnchanged($torrent)) {
            return $this->resourceUnchanged($torrent);
        }

        $result = $this->service->put($torrent);

        if ($result->isSuccessful()) {
            $this->setCache($torrent);
        }

        return $result;
    }

    /**
     * @param $torrent
     *
     * @return bool
     */
    private function isUnchanged(CacheableInterface $torrent)
    {
        return $this->hasCache($torrent) && $this->isTorrentUnchanged($torrent);
    }

    /**
     * @param CacheableInterface $torrent
     *
     * @return bool
     */
    private function isTorrentUnchanged(CacheableInterface $torrent)
    {
        $currentValue = $torrent->cacheValue();
        $cachedValue = $this->getCache($torrent);

        return $currentValue == $cachedValue;
    }

    /**
     * @param CacheableInterface $torrent
     *
     * @return bool
     */
    private function hasCache(CacheableInterface $torrent)
    {
        return $this->cache->has($torrent->cacheKey());
    }

    /**
     * @param CacheableInterface $torrent
     *
     * @return string|mixed
     */
    private function getCache(CacheableInterface $torrent)
    {
        return $this->cache->get($torrent->cacheKey());
    }

    /**
     * @param CacheableInterface $torrent
     *
     * @return mixed
     */
    private function setCache(CacheableInterface $torrent)
    {
        return $this->cache->put($torrent->cacheKey(), $torrent->cacheValue());
    }

    /**
     * @param $torrent
     *
     * @return \Lighthouse\Result
     */
    private function resourceUnchanged($torrent)
    {
        return new Result(ResultCodes::ResourceUnchanged, $torrent);
    }
}
