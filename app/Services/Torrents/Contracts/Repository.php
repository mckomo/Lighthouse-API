<?php namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;

interface Repository
{
    /**
     * @param string $phrase
     * @param array $options
     * @return Torrent[]
     */
    public function search(Query $query);

    /**
     * @param Torrent $torrent
     * @return boolean
     */
    public function store(Torrent $torrent);

    /**
     * @param string $hash
     * @return Torrent
     */
    public function get($hash);
}