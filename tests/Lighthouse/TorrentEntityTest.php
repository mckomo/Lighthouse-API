<?php

namespace Tests\Lighthouse\Torrents;

use Lighthouse\Torrent;

class TorrentEntityTest extends \PHPUnit_Framework_TestCase
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
            'infoHash'          => '96B38CAEED19A26EC338AE3B85AC43335750BFCA',
            'name'              => 'Solarix RELOADED',
            'filename'          => 'solarix-reloaded.torrent',
            'category'          => 'Games',
            'size'              => 1430397537,
            'url'               => 'http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent',
            'magnetLink'        => 'magnet:?xt=urn:btih:96B38CAEED19A26EC338AE3B85AC43335750BFCA&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80',
            'uploadedAt'        => '2015-06-27T16:50:58Z',
            'seedCount'         => 75,
            'peerCount'         => 173,
        ];

        $this->torrent = new Torrent($this->params);
    }

    public function test_hash()
    {
        $this->assertEquals('96B38CAEED19A26EC338AE3B85AC43335750BFCA', $this->torrent->infoHash);
    }

    public function test_has_name()
    {
        $this->assertEquals('Solarix RELOADED', $this->torrent->name);
    }

    public function test_has_filename()
    {
        $this->assertEquals('solarix-reloaded.torrent', $this->torrent->filename);
    }

    public function test_has_category()
    {
        $this->assertEquals('Games', $this->torrent->category);
    }

    public function test_has_size()
    {
        $this->assertEquals(1430397537, $this->torrent->size);
    }

    public function test_has_url()
    {
        $this->assertEquals('http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent', $this->torrent->url);
    }

    public function test_torrent_has_upload_time()
    {
        $this->assertEquals('2015-06-27T16:50:58Z', $this->torrent->uploadedAt);
    }

    public function test_has_seed_count()
    {
        $this->assertEquals(75, $this->torrent->seedCount);
    }

    public function test_has_peer_count()
    {
        $this->assertEquals(173, $this->torrent->peerCount);
    }

    public function test_has_magnet_link()
    {
        $this->assertEquals('magnet:?xt=urn:btih:96B38CAEED19A26EC338AE3B85AC43335750BFCA&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80', $this->torrent->magnetLink);
    }

    /**
     * @dataProvider getHashAndExpectedMagnetLinkTuple
     */
    public function test_fills_magnet_link_using_hash($hash, $expectedMagnetLink)
    {
        $this->params['infoHash'] = $hash;
        $this->params['magnetLink'] = null;

        $torrent = new Torrent($this->params);

        $this->assertEquals($expectedMagnetLink, $torrent->magnetLink);
    }

    /**
     * @dataProvider getNameAndExpectedFilenameTuple
     */
    public function test_fills_filename_using_name($name, $expectedFilename)
    {
        $this->params['name'] = $name;
        $this->params['filename'] = null;

        $torrent = new Torrent($this->params);

        $this->assertEquals($expectedFilename, $torrent->filename);
    }

    public function getHashAndExpectedMagnetLinkTuple()
    {
        return [
            [null, null],
            ['96B38CAEED19A26EC338AE3B85AC43335750BFCA', 'magnet:?xt=urn:btih:96B38CAEED19A26EC338AE3B85AC43335750BFCA&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80'],
        ];
    }

    public function getNameAndExpectedFilenameTuple()
    {
        return [

            [null, null],
            ['Solarix reloaded', 'solarix-reloaded.torrent'],
        ];
    }
}
