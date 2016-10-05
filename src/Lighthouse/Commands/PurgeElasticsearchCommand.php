<?php

namespace Lighthouse\Commands;

use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;

class PurgeElasticsearchCommand
{
    /**
     * @var Index
     */
    private $elastic;

    /**
     * @var string
     */
    private $options;

    /**
     * @param Index $elastic
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->index->delete();
    }
}
