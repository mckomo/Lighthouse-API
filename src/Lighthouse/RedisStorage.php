<?php

namespace Lighthouse;

use Lighthouse\Core\StorageInterface;
use Predis\Client;

class RedisStorage implements StorageInterface
{
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function has($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function put($key, $value)
    {
        return $this->redis->set($key, $value);
    }
}
