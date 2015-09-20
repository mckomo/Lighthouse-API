<?php

namespace Lighthouse\tests\Services\Torrents\RepositoriesTests;

use Elastica\Exception\ConnectionException;
use Elastica\Exception\NotFoundException;
use Lighthouse\Services\Torrents\Repositories\ElasticSearch as Repository;
use Lighthouse\Tests\Support\EntitySampler;
use Mockery;

class ElasticSearchRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $mapperMock;
    private $endpointMock;
    private $resultSetMock;
    private $repository;

    public function setUp()
    {
        $this->mapperMock = Mockery::mock('\Lighthouse\Services\Torrents\Mappers\ElasticSearchResult');
        $this->endpointMock = Mockery::mock('\Elastica\Type');
        $this->resultSetMock = Mockery::mock('\Elastica\ResultSet');
        $this->repository = new Repository($this->endpointMock, $this->mapperMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testByDefaultLimitsQueryToTwenty()
    {
        $query = EntitySampler::sampleQuery();
        $validateSize = function ($query) { return $query->getParam('size') == 20; };

        $this->resultSetMock
            ->shouldReceive('getResults')
            ->once()
            ->andReturn([]);
        $this->endpointMock
            ->shouldReceive('search')
            ->with(Mockery::on($validateSize))
            ->once()
            ->andReturn($this->resultSetMock);

        $this->repository->search($query);
    }

    public function testReturnsNullWhenNoDocumentWasFound()
    {
        $this->endpointMock
            ->shouldReceive('getDocument')
            ->andThrow($this->createNotFoundException());

        $result = $this->repository->get('torrentHash');

        $this->assertNull($result);
    }

    public function testSortsByGivenField()
    {
        $query = EntitySampler::sampleQuery();
        $query->sortBy = 'uploadedAt';

        $validateSort = function ($query) { return array_key_exists('uploadedAt', $query->getParam('sort')); };

        $this->resultSetMock
            ->shouldReceive('getResults')
            ->once()
            ->andReturn([]);
        $this->endpointMock
            ->shouldReceive('search')
            ->with(Mockery::on($validateSort))
            ->once()
            ->andReturn($this->resultSetMock);

        $this->repository->search($query);
    }

    /**
     * @expectedException \Lighthouse\Services\Torrents\Exceptions\RepositoryException
     */
    public function testThrowsRepositoryExceptionOnConnectionErrorWhileFetchingTorrent()
    {
        $this->endpointMock
            ->shouldReceive('getDocument')
            ->andThrow($this->createConnectionException());

        $this->repository->get('torrentHash');
    }

    /**
     * @expectedException \Lighthouse\Services\Torrents\Exceptions\RepositoryException
     */
    public function testThrowsRepositoryExceptionOnConnectionErrorWhileUploadingTorrent()
    {
        $torrent = EntitySampler::sampleTorrent();
        $this->endpointMock
            ->shouldReceive('addDocument')
            ->andThrow($this->createConnectionException());

        $this->repository->store($torrent);
    }

    /**
     * @expectedException \Lighthouse\Services\Torrents\Exceptions\RepositoryException
     */
    public function testThrowsRepositoryExceptionOnConnectionErrorWhileSearchingTorrents()
    {
        $query = EntitySampler::sampleQuery();

        $this->endpointMock
            ->shouldReceive('search')
            ->andThrow($this->createConnectionException());

        $this->repository->search($query);
    }

    private function createConnectionException()
    {
        return new ConnectionException(Mockery::mock('Elastica\Request'));
    }

    private function createNotFoundException()
    {
        return new NotFoundException(Mockery::mock('Elastica\Request'));
    }
}
