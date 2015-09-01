<?php

namespace Lighthouse\tests\App\Commands;

use Lighthouse\Commands\SetupElasticSearch;
use Lighthouse\Handlers\Commands\SetupElasticSearchHandler;
use Mockery;
use Mockery\MockInterface;

class SetupElasticSearchHandlerHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreatesLighthouseIndex()
    {
        $indexMock = $this->setupSimpleIndexMock()
            ->shouldReceive('create')
            ->once()
            ->getMock();

        $clientMock = Mockery::mock('\Elastica\Client')
            ->shouldReceive('getIndex')
            ->with('lighthouse')
            ->once()
            ->andReturn($indexMock)
            ->getMock();

        $setupCommand = new SetupElasticSearch();
        $handler = new SetupElasticSearchHandler($clientMock);

        $handler->handle($setupCommand);
    }

    public function testPurgesLighthouseIndexWithPurgeFlag()
    {
        $shouldPurgeIndex = true;
        $indexMock = $this->setupSimpleIndexMock()
            ->shouldReceive('create')
            ->with(anything(), $shouldPurgeIndex)
            ->once()
            ->getMock();
        $clientMock = $this->setupClientMock($indexMock);

        $setupCommand = new SetupElasticSearch($shouldPurgeIndex);
        $handler = new SetupElasticSearchHandler($clientMock);

        $handler->handle($setupCommand);
    }

    public function testCreatesTorrentType()
    {
        $typeMock = $this->setupSimpleTypeMock();
        $indexMock = $this
            ->setupIgnoringMissingMethodsMockInterface('\Elastica\Index')
            ->shouldReceive('getType')
            ->with('torrent')
            ->once()
            ->andReturn($typeMock)
            ->getMock();
        $clientMock = $this->setupClientMock($indexMock);

        $setupCommand = new SetupElasticSearch();
        $handler = new SetupElasticSearchHandler($clientMock);

        $handler->handle($setupCommand);
    }

    public function testCreatesTorrentTypeMapping()
    {
        $typeMock = $this->setupSimpleTypeMock()
            ->shouldReceive('setMapping')
            ->with(any('\Elastica\Type\Mapping'))
            ->once()
            ->getMock();
        $indexMock = $this->setupSimpleIndexMock($typeMock);
        $clientMock = $this->setupClientMock($indexMock);

        $setupCommand = new SetupElasticSearch();
        $handler = new SetupElasticSearchHandler($clientMock);

        $handler->handle($setupCommand);
    }

    /**
     * @return MockInterface
     */
    private function setupSimpleTypeMock()
    {
        return $this->setupIgnoringMissingMethodsMockInterface('\Elastica\Type');
    }

    /**
     * @param MockInterface $typeMock
     *
     * @return MockInterface
     */
    private function setupSimpleIndexMock(MockInterface $typeMock = null)
    {
        if (is_null($typeMock)) {
            $typeMock = $this->setupSimpleTypeMock();
        }

        return $this->setupIgnoringMissingMethodsMockInterface('\Elastica\Index')
            ->shouldReceive('getType')
            ->andReturn($typeMock)
            ->getMock();
    }

    /**
     * @param MockInterface $indexMockInterface
     *
     * @return MockInterface
     */
    private function setupClientMock(MockInterface $indexMock = null)
    {
        if (is_null($indexMock)) {
            $indexMock = $this->setupSimpleIndexMock();
        }

        return $this->setupIgnoringMissingMethodsMockInterface('\Elastica\Client')
            ->shouldReceive('getIndex')
            ->andReturn($indexMock)
            ->getMock();
    }

    /**
     * @param string $className
     *
     * @return MockInterface
     */
    private function setupIgnoringMissingMethodsMockInterface($className)
    {
        return Mockery::mock($className)->shouldIgnoreMissing();
    }

//    public function testCreatesTorrentTypeWithMapping()
//    {
//        $setupCommand = new SetupElasticSearch();
//        $handler = new SetupElasticSearchHandler($this->clientMock);
//
//        $this->clientMock
//            ->shouldReceive('getIndex')
//            ->with('lighthouse')
//            ->once()
//            ->andReturn($this->indexMock);
//
//        $this->indexMock
//            ->shouldReceive('getType')
//            ->with('torrent')
//            ->once()
//            ->andReturn($this->typeMock);
//
//        $this->typeMock
//            ->shouldReceive('setMapping')
//            ->with(anInstanceOf('Elastica\Type\Mapping'))
//            ->once();
//
//        $handler->handle($setupCommand);
//    }
}
