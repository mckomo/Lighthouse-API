<?php

namespace Tests\Lighthouse\Commands;

use Lighthouse\Commands\PurgeElasticsearchCommand;
use Mockery;
use Mockery\Mock;

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
