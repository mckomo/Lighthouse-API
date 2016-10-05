<?php

namespace App\tests\App\Http;

use Tests\Support\TestCase;

class CorsTest extends TestCase
{
    public function testAjaxRequestsAreEnabled()
    {
        $this->get('/torrents', ['Origin' => 'localhost'])->seeHeader('Access-Control-Allow-Origin', 'localhost');
    }
}
