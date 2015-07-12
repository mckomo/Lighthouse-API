<?php namespace Lighthouse\Tests\Services\Torrents;

use Lighthouse\Tests\Services\Torrents\Support\EntitySampler;
use Mockery;
use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    private $service;
    private $queryValidatorMock;
    private $torrentValidatorMock;
    /**
     * @var
     */
    private $repositoryMock;

    public function setUp()
    {
        $this->repositoryMock = Mockery::mock('Lighthouse\Services\Torrents\Contracts\Repository');
        $this->torrentValidatorMock = Mockery::mock('Lighthouse\Services\Torrents\Validation\Validators\Torrent');
        $this->queryValidatorMock = Mockery::mock('Lighthouse\Services\Torrents\Validation\Validators\Query');

        $this->service = new Service($this->repositoryMock, $this->torrentValidatorMock, $this->queryValidatorMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testValidatesTorrent()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($torrent, null)
            ->andReturn(true);
        $this->repositoryMock
            ->shouldIgnoreMissing();

        $this->service->upload($torrent);
    }

    public function testStoresTorrentsInRepository()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->andReturn(true);
        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with($torrent);

        $this->service->upload($torrent);
    }

    public function testUploadsOnlyValidTorrent()
    {
        $torrent = EntitySampler::sampleTorrent();

        $this->torrentValidatorMock
            ->shouldReceive('isValid')
            ->with($torrent, null)
            ->andReturn(false);
        $this->repositoryMock
            ->shouldNotReceive('store');

        $this->service->upload($torrent);
    }

    public function testValidatesQueryBeforeSearch()
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

    public function testUsesRepositoryToSearchTorrents()
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

    public function testInvalidQueryCancelsSearch()
    {
        $query = $this->getSampleQuery();

        $this->queryValidatorMock
            ->shouldReceive('isValid')
            ->with($query, null)
            ->andReturn(false);
        $this->repositoryMock
            ->shouldNotReceive('search');

        $this->service->search($query);
    }

    public function testUsesRepositoryToFetchTorrent()
    {
        $torrentHash = '96B38CAEED19A26EC338AE3B85AC43335750BFCA';

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($torrentHash);

        $this->service->get($torrentHash);
    }


    private function getSampleQuery()
    {
        return new Query([
            'phrase' => 'Torrent name',
            'size' => 3
        ]);
    }

}