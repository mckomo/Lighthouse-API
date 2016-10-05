<?php

namespace Lighthouse\Commands;

use Predis\Client;

class PurgeRedisCommand
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->redis->flushdb();
    }
}
