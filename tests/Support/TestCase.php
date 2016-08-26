<?php

namespace Tests\Support;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TestCase extends IlluminateTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function seeJsonWithCount($expectedCount, $message = null)
    {
        $array = (array) $this->decodeResponseJson();

        self::assertEquals($expectedCount, count($array), $message);

        return $this;
    }

    protected function seeJsonWithCountGreaterThan($bound, $message = null)
    {
        $array = (array) $this->decodeResponseJson();

        self::assertGreaterThan($bound, count($array), $message);

        return $this;
    }

    /**
     * Assert that the response contains the given JSON.
     *
     * @param  array  $data
     * @param  bool  $negate
     * @return $this
     */
    protected function seeJsonElementsContain(array $elementSubset, $negate = false)
    {
        $method = $negate ? 'assertFalse' : 'assertTrue';

        $elements = Arr::sortRecursive((array) $this->decodeResponseJson());

        foreach ($elements as $element) {

            $actual = json_encode($element);

            foreach ($elementSubset as $key => $value) {

                $expected = $this->formatToExpectedJson($key, $value);

                $this->{$method}(
                    Str::contains($actual, $expected),
                    ($negate ? 'Found unexpected' : 'Unable to find') . " JSON fragment [{$expected}] within [{$actual}]."
                );
            }
        }

        return $this;
    }
}
