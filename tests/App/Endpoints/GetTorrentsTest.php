<?php

namespace Tests\App\Api\V1;

use Tests\Support\TestCase;

class GetTorrentsTest extends TestCase
{
    public function test_responds_with_200()
    {
        $this->json('GET', 'torrents', ['q' => 'windows']);

        $this->assertResponseOk();
    }

    public function test_returned_torrents_have_magnet_links()
    {
        $this->json('GET', 'torrents', ['q' => 'windows']);

        $this->seeJsonStructure(['*' => ['magnetLink']])->seeJsonWithCountGreaterThan(3);
    }

    /**
     * @dataProvider limitProvider
     */
    public function test_limits_searched_torrents($limit)
    {
        $this->json('GET', '/torrents', ['q' => 'windows', 'limit' => $limit]);

        $this->seeJsonWithCount($limit);
    }

    /**
     * @dataProvider fieldProvider
     */
    public function test_sorts_by_field($field)
    {
        $torrents = $this->json('GET', '/torrents', ['q' => 'windows', 'sort_by' => $field])->decodeResponseJson();

        $fields = array_map(function ($torrent) use ($field) {
            return $torrent[$field];
        }, $torrents);
        $sortedFields = $fields;

        rsort($sortedFields);

        $this->assertEquals($sortedFields, $fields);
    }

    /**
     * @dataProvider categoryProvider
     */
    public function test_filters_by_category($category)
    {
        $this->json('GET', '/torrents', ['q' => 'windows', 'category' => $category]);

        $this->seeJsonElementsContain(['category' => $category])->seeJsonWithCountGreaterThan(0, 'This test requires at least one matching torrent');
    }

    public function limitProvider()
    {
        return [[1], [5], [20]];
    }

    public function fieldProvider()
    {
        return [['size'], ['seedCount'], ['peerCount'], ['uploadedAt']];
    }

    public function categoryProvider()
    {
        return [['applications'], ['other'], ['books']];
    }
}
