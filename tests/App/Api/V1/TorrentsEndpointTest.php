<?php

namespace Lighthouse\Tests\App\Api\V1;

use Lighthouse\Tests\Support\TestCase;

class TorrentsEndpointTest extends TestCase
{
    public function testReturnedTorrentsHaveMangnetLinks()
    {
        $response = $this->call('GET', 'api/v1/torrents', ['q' => 'windows']);
        $torrents = json_decode($response->getContent());

        $this->assertGreaterThan(0, count($torrents), 'This test requires at least one torrent in the repository');
        array_walk($torrents, [$this, 'assertHasMagnetLink']);
    }

    private function assertHasMagnetLink($torrent)
    {
        $this->assertContains('magnet:', $torrent->magnetLink);
    }
}