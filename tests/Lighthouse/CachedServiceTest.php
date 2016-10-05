<?php

namespace Tests\Lighthouse;

use Lighthouse\CachedService;
use Lighthouse\Common\ResultCodes;
use Lighthouse\Core\RepositoryInterface;
use Lighthouse\Result;
use Lighthouse\Torrent;
use Mockery;
use Mockery\Mock;
use Tests\Support\EntitySampler;

class CachedServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachedService
     */
    private $cachedService;

    /**
     * @var Mock
     */
    private $serviceMock;

    /**
     * @var Mock
     */
    private $cacheMock;

    /**
     * @var RepositoryInterface
     */
    private $repositoryMock;

    public function setUp()
    {
        $this->serviceMock = Mockery::mock('Lighthouse\Core\ServiceInterface')->shouldIgnoreMissing();
        $this->cacheMock = Mockery::mock('Lighthouse\Core\StorageInterface')->shouldIgnoreMissing();

        $this->cachedService = new CachedService($this->serviceMock, $this->cacheMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_caches_new_torrents()
    {
        $torrent = EntitySampler::sampleTorrent();
        $expectedResult = new Result(ResultCodes::ResourceCreated);

        $this->cacheMock
            ->shouldReceive('has')
            ->once()
            ->with($torrent->cacheKey())
            ->andReturn(false); // Torrent is not cached

        $this->serviceMock
            ->shouldReceive('put')
            ->once()
            ->with($torrent)
            ->andReturn($expectedResult);

        $this->cacheMock
            ->shouldReceive('put')
            ->once()
            ->with($torrent->cacheKey(), $torrent->cacheValue());

        $result = $this->cachedService->put($torrent);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_caches_changed_torrents()
    {
        $torrent = EntitySampler::sampleTorrent();
        $expectedResult = new Result(ResultCodes::Successful);

        $this->cacheMock
            ->shouldReceive('has')
            ->once()
            ->with($torrent->cacheKey())
            ->andReturn(true); // Torrent is already cached
        $this->cacheMock
            ->shouldReceive('get')
            ->once()
            ->with($torrent->cacheKey())
            ->andReturn('DIFFERENT CACHE VALUE'); // Cached torrent has changed

        $this->serviceMock
            ->shouldReceive('put')
            ->once()
            ->with($torrent)
            ->andReturn($expectedResult);

        $this->cacheMock
            ->shouldReceive('put')
            ->once()
            ->with($torrent->cacheKey(), $torrent->cacheValue());

        $result = $this->cachedService->put($torrent);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_skips_unchanged_cached_torrent()
    {
        $torrent = EntitySampler::sampleTorrent();
        $expectedResult = new Result(ResultCodes::ResourceUnchanged, $torrent);

        $this->cacheMock
            ->shouldReceive('has')
            ->once()
            ->with($torrent->cacheKey())
            ->andReturn(true); // Torrent is already cached
        $this->cacheMock
            ->shouldReceive('get')
            ->once()
            ->with($torrent->cacheKey())
            ->andReturn($torrent->cacheValue()); // Cached torrent is unchanged

        $this->serviceMock->shouldNotReceive('put');
        $this->cacheMock->shouldNotReceive('put');

        $result = $this->cachedService->put($torrent);

        $this->assertEquals($expectedResult, $result);
    }
}
