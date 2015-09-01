<?php

namespace Lighthouse\tests\App\Api\V1;

use Lighthouse\Tests\Support\TestCase;
use Mockery;

class TorrentsEndpointTest extends TestCase
{
    private $guzzleMock;

    public function setUp()
    {
        parent::setUp();

        $this->guzzleMock = Mockery::mock('GuzzleHttp\Client');
        $this->app->instance('GuzzleHttp\Client', $this->guzzleMock);
    }

    public function testReturnedTorrentsHaveMangnetLinks()
    {
        $response = $this->call('GET', 'api/v1/torrents', ['q' => 'windows']);
        $torrents = json_decode($response->getContent());

        $this->assertGreaterThan(0, count($torrents), 'This test requires at least one torrent in the repository');
        array_walk($torrents, [$this, 'assertHasMagnetLink']);
    }

    public function testRespondsWithResourceGoneStatusWhenTorrentDownloadFailed()
    {
        $exceptionMock = Mockery::mock('GuzzleHttp\Exception\ClientException');
        $this->guzzleMock
            ->shouldReceive('get')
            ->andThrow($exceptionMock);

        $response = $this->call('GET', 'api/v1/torrents/9C5FB1D3079502196E4990771C9760BF6857D756/file');

        $this->assertEquals(410, $response->getStatusCode());
    }

    private function assertHasMagnetLink($torrent)
    {
        $this->assertContains('magnet:', $torrent->magnetLink);
    }
}
