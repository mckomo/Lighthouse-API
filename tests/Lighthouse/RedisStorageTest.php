<?php

namespace Tests\Lighthouse;

use Lighthouse\CachedService;
use Lighthouse\Common\ResultCodes;
use Lighthouse\Core\RepositoryInterface;
use Lighthouse\Query;
use Lighthouse\RedisStorage;
use Lighthouse\Result;
use Lighthouse\Service;
use Lighthouse\Torrent;
use Lighthouse\Validators\QueryValidator;
use Lighthouse\Validators\TorrentValidator;
use Mockery\Mock;
use Tests\Support\EntitySampler;
use Mockery;

class RedisStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock
     */
    private $redisMock;

    /**
     * @var RedisStorage
     */
    private $redisStorage;

    public function setUp()
    {
        $this->redisMock = Mockery::mock('Predis\Client');

        $this->redisStorage = new RedisStorage($this->redisMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_checks_storage_using_redis()
    {
        $storageKey = 'STORED_KEY';
        $expectedResult = true;

        $this->redisMock
            ->shouldReceive('exists')
            ->once()
            ->with($storageKey)
            ->andReturn($expectedResult); # Torrent is not cached

        $result = $this->redisStorage->has($storageKey);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_fetches_from_storage_using_redis()
    {
        $storageKey = 'STORED_KEY';
        $expectedValue = 'STORED_VALUE';

        $this->redisMock
            ->shouldReceive('get')
            ->once()
            ->with($storageKey)
            ->andReturn($expectedValue); # Torrent is not cached

        $result = $this->redisStorage->get($storageKey);

        $this->assertEquals($expectedValue, $result);
    }

    public function test_puts_to_storage_using_redis()
    {
        $storageKey = 'STORED_KEY';
        $storageValue = 'STORED_VALUE';

        $this->redisMock
            ->shouldReceive('set')
            ->once()
            ->with($storageKey, $storageValue);

        $this->redisStorage->put($storageKey, $storageValue);
    }
}
