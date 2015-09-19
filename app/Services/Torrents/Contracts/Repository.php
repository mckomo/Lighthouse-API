<?php

namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;

interface Repository
{
    /**
     * @param ServiceQuery $query
     *
     * @return Torrent[]
     */
    public function search(ServiceQuery $query);

    /**
     * @param Torrent $torrent
     *
     * @return bool
     */
    public function store(Torrent $torrent);

    /**
     * @param string $hash
     *
     * @return Torrent
     */
    public function get($hash);
}
