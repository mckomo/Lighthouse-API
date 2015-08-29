<?php

namespace Lighthouse\Services\Torrents\Mappers;

use Lighthouse\Services\Torrents\Contracts\Mapper;
use Lighthouse\Services\Torrents\Entities\Torrent;

class ElasticSearchResult implements Mapper
{
    /**
     * @param $data
     *
     * @return Torrent
     */
    public function map($result)
    {
        return new Torrent($result->getData());
    }
}
