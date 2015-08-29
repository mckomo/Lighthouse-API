<?php

use Lighthouse\Services\Torrents\Entities\Torrent;

class TorrentEntityTest extends PHPUnit_Framework_TestCase
{
    private $torrent;
    private $params;

    public function setUp()
    {
        $this->params = [
            'hash'          => '96B38CAEED19A26EC338AE3B85AC43335750BFCA',
            'name'          => 'Solarix-RELOADED',
            'category'      => 'Games',
            'size'          => 1430397537,
            'url'           => 'http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent',
            'magnetLink'    => 'magnet:?xt=urn:btih:{hash}&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80',
            'uploadedAt'    => '2015-06-27T16:50:58Z',
            'seedCount'     => 75,
            'peerCount'     => 173,
        ];

        $this->torrent = new Torrent($this->params);
    }

    public function testHasName()
    {
        $this->assertEquals('96B38CAEED19A26EC338AE3B85AC43335750BFCA', $this->torrent->hash);
    }

    public function testHasCategory()
    {
        $this->assertEquals('Solarix-RELOADED', $this->torrent->name);
    }

    public function testHasSize()
    {
        $this->assertEquals('Games', $this->torrent->category);
    }

    public function testHasUrl()
    {
        $this->assertEquals('http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent', $this->torrent->url);
    }

    public function testTorrentHasUploadTime()
    {
        $this->assertEquals('2015-06-27T16:50:58Z', $this->torrent->uploadedAt);
    }

    public function testHasSeedCount()
    {
        $this->assertEquals(75, $this->torrent->seedCount);
    }

    public function testHasPeerCount()
    {
        $this->assertEquals(173, $this->torrent->peerCount);
    }

    public function testHasMagnetLink()
    {
        $this->assertEquals('magnet:?xt=urn:btih:{hash}&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80', $this->torrent->magnetLink);
    }

    public function testSetsMagnetLinkIfNotGiven()
    {
        unset($this->params['magnetLink']);

        $torrent = new Torrent($this->params);

        $this->assertContains('magnet:', $torrent->magnetLink);
    }
}
