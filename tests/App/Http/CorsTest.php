<?php

namespace Lighthouse\tests\App\Http;

use Illuminate\Http\Request;
use Lighthouse\Tests\Support\TestCase;

class CorsTest extends TestCase
{
    public function testAjaxRequestsAreEnabled()
    {
        $request = Request::create('GET', 'api/v1/torrents');
        $request->headers->set('Origin', 'my-domain.com');

        $response = $this->app->make('Illuminate\Contracts\Http\Kernel')->handle($request);
        $allowedOriginsHeader = $response->headers->get('Access-Control-Allow-Origin');

        $this->assertEquals('my-domain.com', $allowedOriginsHeader);
    }
}
