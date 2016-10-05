<?php

namespace Tests\Lighthouse;

use Elastica\Exception\ConnectionException;
use Elastica\Exception\NotFoundException;
use Lighthouse\ElasticsearchRepository;
use Lighthouse\Torrent;
use Mockery;
use Tests\Support\EntitySampler;

class ElasticsearchRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $mapperMock;
    private $endpointMock;
    private $resultSetMock;

    /**
     * @var ElasticsearchRepository
     */
    private $repository;

    public function setUp()
    {
        $this->endpointMock = Mockery::mock('\Elastica\Type');
        $this->resultSetMock = Mockery::mock('\Elastica\ResultSet');

        $this->repository = new ElasticsearchRepository($this->endpointMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_by_default_limits_query_to_twenty()
    {
        $query = EntitySampler::sampleQuery();
        $validateSize = function ($query) {
            return $query->getParam('size') == 20;
        };

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

        $validateSort = function ($query) {
            return array_key_exists('uploadedAt', $query->getParam('sort'));
        };

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
     * @expectedException \Lighthouse\Exceptions\RepositoryException
     */
    public function test_throws_RepositoryException_on_connection_error_while_fetching_torrent()
    {
        $this->endpointMock
            ->shouldReceive('getDocument')
            ->andThrow($this->createConnectionException());

        $this->repository->get('torrentHash');
    }

    /**
     * @expectedException \Lighthouse\Exceptions\RepositoryException
     */
    public function test_throws_RepositoryException_on_connection_error_while_saving_torrent()
    {
        $this->endpointMock
            ->shouldReceive('addDocument')
            ->andThrow($this->createConnectionException());

        $this->repository->put(new Torrent());
    }

    /**
     * @expectedException \Lighthouse\Exceptions\RepositoryException
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
