<?php

namespace Lighthouse\Core;

use Lighthouse\Torrent;

interface TorrentMapperInterface
{
    /**
     * @param mixed $data
     *
     * @return Torrent
     */
    public function map($data);
}
