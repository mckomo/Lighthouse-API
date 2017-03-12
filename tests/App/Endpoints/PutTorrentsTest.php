<?php

namespace Tests\App\Api\V1;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Support\EntitySampler;
use Tests\Support\TestCase;

class PutTorrentsTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var array
     */
    private $torrentParams;

    protected function setUp()
    {
        parent::setUp();

        $this->torrentParams = EntitySampler::sampleTorrent()->toArray();
    }

    public function test_responds_with_201()
    {
        $this->json('PUT', 'torrents', $this->torrentParams);

        $this->assertResponseStatus(201);
    }

    public function test_responds_with_202_when_request_is_duplicated()
    {
        $this->json('PUT', 'torrents', $this->torrentParams);

        $this->assertResponseStatus(202);
    }

    public function test_returns_passed_torrent_when_successful() {
        $this->json('PUT', 'torrents', $this->torrentParams);

        $this->seeJson($this->torrentParams);
    }

    public function test_responds_with_400_when_invalid_entity_passed()
    {
        $this->json('PUT', 'torrents', ['name'  => 'Windows example', 'size'  => 123]);

        $this->assertResponseStatus(400);
    }

    public function test_returns_error_when_invalid_entity_passed()
    {
        $this->json('PUT', 'torrents', ['name'  => 'Windows example', 'size'  => 123]);

        $this->seeJsonStructure(['error' => ['message', 'attachments']]);
    }
}
