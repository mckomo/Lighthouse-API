<?php

namespace Tests\App\Api\V1;

use Tests\Support\TestCase;

class GetTorrentsWithHashTest extends TestCase
{
    public function test_responds_with_200()
    {
        $this->json('GET', 'torrents/396F31CBA7221279B8E498EB8787D47A598A79DC');

        $this->assertResponseOk();
    }
}
