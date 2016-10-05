<?php

namespace Tests\Lighthouse\Commands;

use Elastica\Index;
use Elastica\Type;
use Lighthouse\Commands\PurgeElasticsearchCommand;
use Lighthouse\Commands\SetupElasticsearchCommand;
use Mockery;
use Mockery\Mock;
use Mockery\MockInterface;

class PurgeElasticsearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock
     */
    private $indexMock;

    /**
     * @var Mock
     */
    private $typeMock;

    /**
     * @var PurgeElasticsearchCommand
     */
    private $purgeCommand;

    public function setUp()
    {
        $this->indexMock = Mockery::mock('\Elastica\Index');

        $this->purgeCommand = new PurgeElasticsearchCommand($this->indexMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_deletes_index()
    {
        $this->indexMock
            ->shouldReceive('delete')
            ->once();

        $this->purgeCommand->handle();
    }
}
