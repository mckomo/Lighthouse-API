<?php

namespace Tests\Lighthouse;

use Lighthouse\Core\RepositoryInterface;
use Lighthouse\Service;
use Lighthouse\Validators\QueryValidator;
use Lighthouse\Validators\TorrentValidator;
use Mockery;
use Tests\Support\EntitySampler;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var QueryValidator
     */
    private $queryValidatorMock;

    /**
     * @var TorrentValidator
     */
    private $torrentValidatorMock;

    /**
     * @var RepositoryInterface
     */
    private $repositoryMock;

    public function setUp()
    {
        $this->repositoryMock = Mockery::mock('Lighthouse\Core\RepositoryInterface');
        $this->torrentValidatorMock = Mockery::mock('Lighthouse\Validators\TorrentValidator');
        $this->queryValidatorMock = Mockery::mock('Lighthouse\Validators\QueryValidator');

        $this->service = new Service($this->repositoryMock, $this->torrentValidatorMock, $this->queryValidatorMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_validates_torrent_before_save()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($torrent, null)
            ->andReturn(true);
        $this->repositoryMock
            ->shouldIgnoreMissing();

        $this->service->put($torrent);
    }

    public function test_puts_torrent_in_repository()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->andReturn(true);
        $this->repositoryMock
            ->shouldReceive('put')
            ->once()
            ->with($torrent);

        $this->service->put($torrent);
    }

    public function test_puts_only_valid_torrent()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->with($torrent, null)
            ->andReturn(false);
        $this->repositoryMock
            ->shouldNotReceive('put');

        $this->service->put($torrent);
    }

    public function test_validates_query_before_search()
    {
        $query = EntitySampler::sampleQuery();

        $this->queryValidatorMock
            ->shouldReceive('isValid')
            ->with($query, null)
            ->andReturn(true);

        $this->repositoryMock
            ->shouldIgnoreMissing();

        $this->service->search($query);
    }

    public function test_searches_torrents_in_repository()
    {
        $query = EntitySampler::sampleQuery();

        $this->queryValidatorMock
            ->shouldReceive('isValid')
            ->andReturn(true);
        $this->repositoryMock
            ->shouldReceive('search')
            ->once()
            ->with($query);

        $this->service->search($query);
    }

    public function testValidQueryTriggersSearch()
    {
        $query = EntitySampler::sampleQuery();

        $this->queryValidatorMock
            ->shouldReceive('isValid')
            ->with($query, null)
            ->andReturn(true);
        $this->repositoryMock
            ->shouldReceive('search')
            ->with($query)
            ->once();

        $this->service->search($query);
    }

    public function test_invalid_query_cancels_search()
    {
        $query = EntitySampler::sampleQuery();

        $this->queryValidatorMock
            ->shouldReceive('isValid')
            ->with($query, null)
            ->andReturn(false);
        $this->repositoryMock
            ->shouldNotReceive('search');

        $this->service->search($query);
    }

    public function test_gets_torrent_from_repository()
    {
        $torrentHash = '96B38CAEED19A26EC338AE3B85AC43335750BFCA';

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($torrentHash);

        $this->service->get($torrentHash);
    }
}
