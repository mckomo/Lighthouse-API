<?php

namespace Lighthouse\tests\App\Http;

use Illuminate\Http\Request;
use Lighthouse\Tests\Support\TestCase;

class CorsTest extends TestCase
{
    public function testAjaxRequestsAreEnabled()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

//        $response = $this->call('GET', 'api/v1/torrents', ['q' => 'ubuntu'], [], [], ['Origin' => 'http://my-domain.com'], ['Referer' => 'http://my-domain.com']);
//
//        $allowedOriginsHeader = $response->headers->get('Access-Control-Allow-Origin');
//
//        var_dump($response->headers);
//
//        $this->assertEquals('my-domain.com', $allowedOriginsHeader);
    }
}
