<?php

namespace App\tests\App\Http;

use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Tests\Support\TestCase;

class AuthMiddlewareTest extends TestCase
{
    /**
     * @var AuthMiddleware
     */
    private $middleware;

    /**
     * @var \Closure
     */
    private $next;

    protected function setUp()
    {
        parent::setUp();

        $this->middleware = new AuthMiddleware();
        $this->next = function() { return response('Response from next', 200); };
    }

    /**
     * @dataProvider safeRequestMethods
     */
    public function test_does_not_validate_access_token_on_save_request($safeMethod)
    {
        $request = Request::create('/resource', $safeMethod, ['access_token' => 'InvalidAccessToken']);

        $response = $this->middleware->handle($request, $this->next);

        $this->assertEquals(200, $response->status());
    }

    /**
     * @dataProvider unsafeRequestMethods
     */
    public function test_responds_with_401_without_valid_access_token_on_unsafe_request($unsafeMethod)
    {
        $request = Request::create('/resource', $unsafeMethod, ['access_token' => 'InvalidAccessToken']);

        $response = $this->middleware->handle($request, $this->next);

        $this->assertEquals(401, $response->status());
    }

    public function test_responds_with_error_message_in_JSON_format()
    {
        $request = Request::create('/resource', 'POST', ['access_token' => 'InvalidAccessToken']);

        $response = $this->middleware->handle($request, $this->next);

        $this->assertEquals(json_encode(['error' => ['message' => 'Unauthorized']]), $response->content());
    }

    public function test_calls_next_middleware_if_access_token_is_valid()
    {
        $request = Request::create('/resource', 'POST', ['access_token' => env('APP_ACCESS_TOKEN')]);

        $response = $this->middleware->handle($request, $this->next);

        $this->assertEquals('Response from next', $response->content());
    }

    public function safeRequestMethods()
    {
        return [['GET'], ['OPTIONS']];
    }

    public function unsafeRequestMethods()
    {
        return [['POST'], ['PUT'], ['PATCH'], ['DELETE']];
    }
}