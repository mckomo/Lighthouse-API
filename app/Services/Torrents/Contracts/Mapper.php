<?php

namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Entities\Torrent;

interface Mapper
{
    /**
     * @param mixed $data
     *
     * @return Torrent
     */
    public function map($data);
}
