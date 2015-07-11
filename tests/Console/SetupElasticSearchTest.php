<?php namespace Lighthouse\Tests\Commands;

use Lighthouse\Console\Commands\SetupElasticSearch;
use Mockery;

class SetupElasticSearchTest extends \PHPUnit_Framework_TestCase
{
    private $command;
    private $clientMock;
    private $indexMock;
    private $typeMock;
    private $repository;

    public function setUp()
    {
        $this->clientMock = Mockery::mock('\Elastica\Client');
        $this->indexMock = Mockery::mock('\Elastica\Index');
        $this->typeMock = Mockery::mock('\Elastica\Type');

        $this->command = new SetupElasticSearch($this->clientMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreatesTorrentType()
    {
        $this->clientMock
            ->shouldReceive('getIndex')
            ->with('lighthouse')
            ->once()
            ->andReturn($this->indexMock);

        $this->indexMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($this->indexMock)
            ->shouldReceive('getType')
            ->with('torrent')
            ->once()
            ->andReturn($this->typeMock);

        $this->typeMock
            ->shouldReceive('setMapping')
            ->with(anInstanceOf('Elastica\Type\Mapping'))
            ->once();

        $this->command->fire();
    }


}