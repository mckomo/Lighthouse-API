<?php

namespace Tests\Lighthouse\Validators;

use Lighthouse\Query;
use Tests\Support\EntitySampler;
use Lighthouse\Validators\QueryValidator;

class QueryValidatorTest extends \PHPUnit_Framework_TestCase
{
    const QUERY_LIMIT = 100;

    /**
     * @var QueryValidator
     */
    private $validator;

    /**
     * @var Query
     */
    private $query;

    public function setUp()
    {
        $this->query = EntitySampler::sampleQuery();
        $this->validator = new QueryValidator();
    }

    public function test_succeeds_with_valid_query()
    {
        $result = $this->validator->isValid($this->query);

        $this->assertTrue($result);
    }

    public function test_succeeds_without_category()
    {
        $this->query->category = null;

        $result = $this->validator->isValid($this->query);

        $this->assertTrue($result);
    }

    public function test_succeeds_without_limit()
    {
        $this->query->limit = null;

        $result = $this->validator->isValid($this->query);

        $this->assertTrue($result);
    }

    public function test_fails_with_null()
    {
        $result = $this->validator->isValid(null);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getNullAndEmptyString
     */
    public function testFailsWithEmptyPhrase($invalidPhrase)
    {
        $this->query->phrase = $invalidPhrase;

        $result = $this->validator->isValid($this->query);

        $this->assertFalse($result);
    }

    public function test_fails_with_unsupported_category()
    {
        $this->query->category = 'Unsupported category';

        $result = $this->validator->isValid($this->query);

        $this->assertFalse($result);
    }

    public function test_fails_with_invalid_sort_field()
    {
        $this->query->sortBy = 'NonsortableField';

        $result = $this->validator->isValid($this->query);

        $this->assertFalse($result);
    }

    public function test_fails_with_invalid_sort_order()
    {
        $this->query->sortOrder = 'Invalid sort order';

        $result = $this->validator->isValid($this->query);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getZeroAndAboveLimit
     */
    public function test_fails_without_of_range_limit($invalidLimit)
    {
        $this->query->limit = $invalidLimit;

        $result = $this->validator->isValid($this->query);

        $this->assertFalse($result);
    }

    public function test_returns_validator_error()
    {
        $this->query->phrase = '';

        $this->validator->isValid($this->query, $errors);

        $this->assertEquals(1, count($errors));
    }

    public function test_appends_validation_errors()
    {
        $this->query->phrase = '';
        $this->query->limit = self::QUERY_LIMIT + 1;

        $this->validator->isValid($this->query, $errors);

        $this->assertEquals(2, count($errors));
    }

    /**
     * @return array
     */
    public function getNullAndEmptyString()
    {
        return [[null], ['']];
    }

    /**
     * @return array
     */
    public function getZeroAndAboveLimit()
    {
        return [[0], [self::QUERY_LIMIT + 1]];
    }
}
