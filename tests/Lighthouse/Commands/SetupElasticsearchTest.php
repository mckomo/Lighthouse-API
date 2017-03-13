<?php

namespace Tests\Lighthouse\Commands;

use Lighthouse\Commands\SetupElasticsearchCommand;
use Mockery;
use Mockery\Mock;

class SetupElasticsearchTest extends \PHPUnit_Framework_TestCase
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
     * @var SetupElasticsearchCommand
     */
    private $setupCommand;

    public function setUp()
    {
        $this->indexMock = Mockery::mock('\Elastica\Index');
        $this->typeMock = Mockery::mock('\Elastica\Type');

        $this->setupCommand = new SetupElasticsearchCommand($this->indexMock, $this->typeMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_sets_properties_of_index()
    {
        $this->indexMock
            ->shouldReceive('create')
            ->with(hasEntry('analysis', typeOf('array')))
            ->once();
        $this->typeMock
            ->shouldIgnoreMissing();

        $this->setupCommand->handle();
    }

    public function test_sets_type_mapping()
    {
        $this->indexMock
            ->shouldIgnoreMissing();
        $this->typeMock
            ->shouldReceive('setMapping')
            ->with(anInstanceOf('Elastica\Type\Mapping'))
            ->once();

        $this->setupCommand->handle();
    }
}
