<?php

namespace Lighthouse\Commands;


class SetupElasticSearch extends Command
{
    private $shouldPurge;

    /**
     * @param bool $shouldPurge
     */
    public function __construct($shouldPurge = false)
    {
        $this->shouldPurge = boolval($shouldPurge);
    }

    /**
     * @return bool
     */
    public function shouldPurgeExistingIndex()
    {
        return $this->shouldPurge;
    }
}