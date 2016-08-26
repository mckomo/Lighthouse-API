<?php

namespace Lighthouse\Core;

use Lighthouse\Query;
use Lighthouse\Torrent;

interface RepositoryInterface
{
    /**
     * @param string $infoHash
     *
     * @return Torrent
     */
    public function get($infoHash);

    /**
     * @param Query $query
     *
     * @return Entity[]
     */
    public function search(Query $query);

    /**
     * @param Entity $torrent
     *
     * @return bool
     */
    public function put(Torrent $torrent);

}
