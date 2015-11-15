<?php

namespace Lighthouse\tests\App\Api\V1;

use Lighthouse\Tests\Support\TestCase;

class TorrentsEndpointTest extends TestCase
{
    public function testReturnedTorrentsHaveMagnetLinks()
    {
        $response = $this->call('GET', 'api/v1/torrents', ['q' => 'windows']);
        $torrents = json_decode($response->getContent());

        $this->assertGreaterThan(0, count($torrents), 'This test requires at least one torrent in the repository');
        array_walk($torrents, [$this, 'assertHasMagnetLink']);
    }

    /**
     * @dataProvider limitProvider
     */
    public function testLimitsReturnedTorrents($limit)
    {
        $responseContent = $this
            ->call('GET', 'api/v1/torrents', ['q' => 'windows', 'limit' => $limit])
            ->getContent();

        $returnedTorrents = json_decode($responseContent);

        $this->assertEquals($limit, count($returnedTorrents), 'This test requires at least two torrents in the repository');
    }

    public function limitProvider()
    {
        return [[1], [5], [23]];
    }

    /**
     * @dataProvider fieldProvider
     */
    public function testSortsByField($field)
    {
        $responseContent = $this
            ->call('GET', 'api/v1/torrents', ['q' => 'windows', 'sort_by' => $field])
            ->getContent();
        $returnedTorrents = json_decode($responseContent);

        $extractedFields = array_map(function ($torrent) use ($field) {
            return $torrent->$field;
        }, $returnedTorrents);
        $sortedFields = $this->sortCopy($extractedFields);

        $this->assertGreaterThan(1, count($returnedTorrents), 'This test requires at least two torrents in the repository');
        $this->assertEquals($sortedFields, $extractedFields);
    }

    public function fieldProvider()
    {
        return [['size'], ['seedCount'], ['peerCount'], ['uploadedAt']];
    }

    /**
     * @dataProvider categoryProvider
     */
    public function testFiltersByCategory($category)
    {
        $responseContent = $this
            ->call('GET', 'api/v1/torrents', ['q' => 'windows', 'category' => strtolower($category)])
            ->getContent();

        $returnedTorrents = json_decode($responseContent);
        $filteredTorrents = $this->selectMatching(json_decode($responseContent), function ($torrent) use ($category) {
            return $torrent->category == $category;
        });

        $this->assertGreaterThan(0, count($returnedTorrents), 'This test requires at least one torrent in the repository');
        $this->assertEquals($filteredTorrents, $returnedTorrents);
    }

    public function categoryProvider()
    {
        return [['Applications'], ['Other'], ['Books']];
    }

    private function assertHasMagnetLink($torrent)
    {
        $this->assertContains('magnet:', $torrent->magnetLink);
    }

    private function sortCopy(array $array)
    {
        $stortedArray = $array;
        rsort($stortedArray); // Desc order

        return $stortedArray;
    }

    private function selectMatching(array $array, \Closure $matcher)
    {
        array_filter($array, $matcher);

        return $array;
    }
}
