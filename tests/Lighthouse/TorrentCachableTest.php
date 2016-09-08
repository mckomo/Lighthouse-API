<?php

namespace Tests\Lighthouse\Torrents;

use Lighthouse\Torrent;

class TorrentCachableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Torrent
     */
    private $torrent;

    /**
     * @var array
     */
    private $params;

    public function setUp()
    {
        $this->params = [
            'infoHash'      => '96B38CAEED19A26EC338AE3B85AC43335750BFCA',
            'name'          => 'Solarix RELOADED',
            'filename'      => 'solarix-reloaded.torrent',
            'category'      => 'Games',
            'size'          => 1430397537,
            'url'           => 'http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent',
            'magnetLink'    => 'magnet:?xt=urn:btih:96B38CAEED19A26EC338AE3B85AC43335750BFCA&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80',
            'uploadedAt'    => '2015-06-27T16:50:58Z',
            'seedCount'     => 75,
            'peerCount'     => 173,
        ];

        $this->torrent = new Torrent($this->params);
    }

    public function test_uses_info_hash_as_cache_key()
    {
        $this->assertEquals('96B38CAEED19A26EC338AE3B85AC43335750BFCA', $this->torrent->cacheKey());
    }

    public function test_cache_value_is_md5_string()
    {
        $this->assertRegExp('/^[a-f0-9]{32}$/', $this->torrent->cacheValue());
    }

    public function test_torrents_have_different_cache_values()
    {
        $paramsOne = $this->params;

        $paramsTwo = $paramsOne;
        $paramsTwo['uploadedAt'] = '2016-12-01T08:15:31Z';

        $torrentOne = new Torrent($paramsOne);
        $torrentTwo = new Torrent($paramsTwo);

        $this->assertNotEquals($torrentOne->cacheValue(), $torrentTwo->cacheValue(), 'Two torrents cannot have same cache value');
    }
}
