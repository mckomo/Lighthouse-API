<?php namespace Lighthouse\Tests\Services\Torrents\Support;

use Lighthouse\Services\Torrents\Entities\Query;
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
            'category'      => 'Games',
            'size'          => 1430397537,
            'url'           => 'http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent',
            'uploadedAt'    => '2015-06-27T16:50:58Z',
            'seedCount'     => 75,
            'peerCount'     => 175
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
            'category'      => 'games'
        ]);
    }
}