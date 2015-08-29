<?php

namespace Lighthouse\tests\Services\Torrents\MappersTests;

use Lighthouse\Services\Torrents\Mappers\KickassExportData as Mapper;

class KickassExportDataMapperTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;
    private $data = '96B38CAEED19A26EC338AE3B85AC43335750BFCA|Solarix-RELOADED|Games|https://kat.cr/solarix-reloaded-t10582161.html|http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent|2272891893|31|3|173|75|1430397537';

    public function setUp()
    {
        $this->mapper = new Mapper();
    }

    public function testMapsToEntity()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertInstanceOf('\Lighthouse\Services\Torrents\Entities\Torrent', $torrent);
    }

    public function testMapsHash()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertEquals('96B38CAEED19A26EC338AE3B85AC43335750BFCA', $torrent->hash);
    }

    public function testMapsName()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertEquals('Solarix-RELOADED', $torrent->name);
    }

    public function testMapsCategory()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('Games', $torrent->category);
    }

    public function testMapsUrl()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('http://torcache.net/torrent/96B38CAEED19A26EC338AE3B85AC43335750BFCA.torrent', $torrent->url);
    }

    public function testMapsSize()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(2272891893, $torrent->size);
    }

    public function testMapsUploadTimeToISO8601()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame('2015-04-30T12:38:57Z', $torrent->uploadedAt);
    }

    public function testMapsSeedsCount()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(173, $torrent->seedCount);
    }

    public function testMapsPeersCount()
    {
        $torrent = $this->mapper->map($this->data);

        $this->assertSame(75, $torrent->peerCount);
    }

    public function testReturnsNullWithInvalidDataFormat()
    {
        $invalidLine = '6a196e9f718a83721bc0c0faf6218e6f54573a10|Torrent with invalid data format';
        $result = $this->mapper->map($invalidLine);

        $this->assertNull($result);
    }
}
