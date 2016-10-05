<?php

namespace Tests\Lighthouse\Commands;

use Elastica\Index;
use Elastica\Type;
use Lighthouse\Commands\PurgeElasticsearchCommand;
use Lighthouse\Commands\PurgeRedisCommand;
use Lighthouse\Commands\SetupElasticsearchCommand;
use Mockery;
use Mockery\Mock;
use Mockery\MockInterface;

class PurgeRedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock
     */
    private $clientMock;

    /**
     * @var PurgeElasticsearchCommand
     */
    private $purgeCommand;

    public function setUp()
    {
        $this->clientMock = Mockery::mock('\Predis\Client');

        $this->purgeCommand = new PurgeRedisCommand($this->clientMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_flushes_storage()
    {
        $this->clientMock
            ->shouldReceive('flushdb')
            ->once();

        $this->purgeCommand->handle();
    }
}
