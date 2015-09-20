<?php

namespace Lighthouse\tests\Support;

use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;

class EntitySampler
{
    /**
     * @return Torrent
     */
    public static function sampleTorrent()
    {
        return new Torrent([
            'hash'          => '96B38CAEED19A26EC338AE3B85AC43335750BFCA',
            'name'          => 'Solarix-RELOADED',
            'filename'      => 'solarix-reloaded.torrent',
            'category'      => 'Games',
            'size'          => 1430397537,
            'url'           => 'http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent',
            'uploadedAt'    => '2015-06-27T16:50:58Z',
            'seedCount'     => 75,
            'peerCount'     => 175,
        ]);
    }

    /**
     * @return ServiceQuery
     */
    public static function sampleQuery()
    {
        return new ServiceQuery([
            'phrase'        => 'Solarix-RELOADED',
            'size'          => 20,
            'category'      => 'games',
            'sortBy'        => 'uploadedAt',
            'sortOrder'     => 'asc',
        ]);
    }
}
