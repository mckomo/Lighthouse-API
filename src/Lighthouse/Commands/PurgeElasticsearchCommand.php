<?php

namespace Lighthouse\Commands;

use Elastica\Index;

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
        if ($this->index->exists()) {
            $this->index->delete();
        }
    }
}
