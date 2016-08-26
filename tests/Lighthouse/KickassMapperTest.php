<?php

namespace Tests\Lighthouse;

use Lighthouse\TorrentMappers\KickassMapper;

class CsvTorrentMapperTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;
    private $data = '96B38CAEED19A26EC338AE3B85AC43335750BFCA|Solarix-RELOADED|Games|https://kat.cr/solarix-reloaded-t10582161.html|http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent|2272891893|31|3|173|75|1430397537';

    public function setUp()
    {
        $this->mapper = new KickassMapper();
    }

    public function test_maps_to_torrent_object()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertInstanceOf('\Lighthouse\Torrent', $torrent);
    }

    public function test_maps_hash()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertEquals('96B38CAEED19A26EC338AE3B85AC43335750BFCA', $torrent->infoHash);
    }

    public function test_maps_Name()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertEquals('Solarix-RELOADED', $torrent->name);
    }

    public function test_maps_category()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('Games', $torrent->category);
    }

    public function test_maps_url()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent', $torrent->url);
    }

    public function test_maps_size()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(2272891893, $torrent->size);
    }

    public function test_maps_upload_time_to_ISO8601()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('2015-04-30T12:38:57Z', $torrent->uploadedAt);
    }

    public function test_maps_seeds_count()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(173, $torrent->seedCount);
    }

    public function test_maps_peers_count()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(75, $torrent->peerCount);
    }

    public function test_returns_null_with_invalid_data_format()
    {
        $invalidLine = '6a196e9f718a83721bc0c0faf6218e6f54573a10|Torrent with invalid data format';

        $result = $this->mapper->map($invalidLine);

        $this->assertNull($result);
    }
}
