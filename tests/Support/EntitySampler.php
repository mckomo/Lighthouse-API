<?php

namespace Tests\Support;

use Lighthouse\Query;
use Lighthouse\Torrent;

class EntitySampler
{
    /**
     * @return Torrent
     */
    public static function sampleTorrent()
    {
        return new Torrent([
            'infoHash'      => '96B38CAEED19A26EC338AE3B85AC43335750BFCA',
            'name'          => 'Solarix RELOADED',
            'category'      => 'games',
            'size'          => 1430397537,
            'magnetLink'    => 'magnet:?xt=urn:btih:96B38CAEED19A26EC338AE3B85AC43335750BFCA&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80',
            'uploadedAt'    => '2015-06-27T16:50:58Z',
            'seedCount'     => 75,
            'peerCount'     => 173,
        ]);
    }

    /**
     * @return Query
     */
    public static function sampleQuery()
    {
        return new Query([
            'phrase'        => 'Solarix-RELOADED',
            'size'          => 20,
            'category'      => 'games',
            'sortBy'        => 'uploadedAt',
            'sortOrder'     => 'asc',
        ]);
    }
}
