<?php

namespace Lighthouse\tests\App\Api\V1;

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

    public function testSortsByUploadTime()
    {
        $response = $this->call('GET', 'api/v1/torrents', ['q' => 'windows', 'sort_by' => 'uploadedAt']);
        $returnedTorrents = json_decode($response->getContent());
        $sortedTorrents = json_decode($response->getContent());

        usort($sortedTorrents, function ($lhs, $rhs) {
            return strcmp($rhs->uploadedAt, $lhs->uploadedAt); // Desc order
        });

        $this->assertGreaterThan(1, count($returnedTorrents), 'This test requires at least two torrents in the repository');
        $this->assertEquals($sortedTorrents, $returnedTorrents);
    }

    private function assertHasMagnetLink($torrent)
    {
        $this->assertContains('magnet:', $torrent->magnetLink);
    }
}
